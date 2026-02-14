<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Aplica o filtro de company_id à query de notificações.
     * Centraliza a lógica que antes estava duplicada em 5 métodos.
     */
    private function applyCompanyFilter($query, ?int $companyId)
    {
        if ($companyId) {
            $query->where(function ($q) use ($companyId) {
                $q->whereJsonContains('data->company_id', $companyId)
                  ->orWhereNull('data->company_id');
            });
        }

        return $query;
    }

    /**
     * Filtra notificações cujo expires_at já passou.
     * Notificações expiradas são removidas da listagem.
     */
    private function excludeExpiredNotifications($query)
    {
        $now = Carbon::now()->toISOString();
        
        return $query->where(function ($q) use ($now) {
            // Inclui notificações sem expires_at OU com expires_at no futuro
            $q->whereNull('data->expires_at')
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.expires_at')) >= ?", [$now]);
        });
    }

    /**
     * Retorna a contagem de não lidas filtrada por empresa.
     * Exclui notificações expiradas da contagem.
     */
    private function getUnreadCount(?int $companyId): int
    {
        $query = Auth::user()->unreadNotifications();
        $query = $this->applyCompanyFilter($query, $companyId);
        $query = $this->excludeExpiredNotifications($query);
        return $query->count();
    }

    /**
     * Retorna as notificações do usuário logado.
     * Usa NotificationResource para transformação padronizada.
     * Exclui notificações expiradas.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = session('active_company_id');

        $query = $this->applyCompanyFilter($user->notifications(), $companyId);
        $query = $this->excludeExpiredNotifications($query);
        $notifications = $query->latest()->take(20)->get();

        return response()->json([
            'success' => true,
            'notifications' => NotificationResource::collection($notifications),
            'unread_count' => $this->getUnreadCount($companyId),
        ]);
    }

    /**
     * Retorna apenas a contagem de notificações não lidas.
     * Chave padronizada: unread_count (consistente com index).
     */
    public function unreadCount()
    {
        $companyId = session('active_company_id');

        return response()->json([
            'success' => true,
            'unread_count' => $this->getUnreadCount($companyId),
        ]);
    }

    /**
     * Marca uma notificação como lida.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notificação não encontrada',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificação marcada como lida',
        ]);
    }

    /**
     * Marca todas as notificações como lidas.
     */
    public function markAllAsRead()
    {
        $companyId = session('active_company_id');
        $query = $this->applyCompanyFilter(Auth::user()->unreadNotifications(), $companyId);
        $query->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Todas as notificações foram marcadas como lidas',
        ]);
    }

    /**
     * Remove uma notificação.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notificação não encontrada',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificação removida',
        ]);
    }

    /**
     * Remove todas as notificações lidas.
     */
    public function destroyRead()
    {
        $companyId = session('active_company_id');
        $query = $this->applyCompanyFilter(Auth::user()->readNotifications(), $companyId);
        $deleted = $query->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deleted} notificações removidas",
        ]);
    }

    /**
     * Retorna todas as notificações paginadas (JSON para o drawer).
     * Suporta filtro por status: all, unread, read.
     * Exclui notificações expiradas.
     */
    public function all(Request $request)
    {
        $companyId = session('active_company_id');
        $filter = $request->get('filter', 'all');
        $page = $request->get('page', 1);

        $user = Auth::user();

        $query = match ($filter) {
            'unread' => $user->unreadNotifications(),
            'read'   => $user->readNotifications(),
            default  => $user->notifications(),
        };

        $query = $this->applyCompanyFilter($query, $companyId);
        $query = $this->excludeExpiredNotifications($query);
        $paginated = $query->latest()->paginate(15, ['*'], 'page', $page);

        return response()->json([
            'success'      => true,
            'notifications' => NotificationResource::collection($paginated),
            'unread_count' => $this->getUnreadCount($companyId),
            'pagination'   => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'total'        => $paginated->total(),
                'has_more'     => $paginated->hasMorePages(),
            ],
        ]);
    }

    /**
     * Exibe a página completa de notificações.
     * Exclui notificações expiradas.
     */
    public function page(Request $request)
    {
        $companyId = session('active_company_id');
        $query = $this->applyCompanyFilter(Auth::user()->notifications(), $companyId);
        $query = $this->excludeExpiredNotifications($query);
        $notifications = $query->latest()->paginate(20);

        return view('app.notifications.index', compact('notifications'));
    }
}
