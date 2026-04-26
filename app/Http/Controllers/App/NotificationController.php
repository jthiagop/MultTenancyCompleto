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
     * Filtra por company_id usando a coluna física (após backfill).
     *
     * Mantém `orWhereNull` apenas para registros antigos cujo backfill
     * ainda não foi executado — após `notifications:backfill-columns`,
     * todos terão valor e o fallback fica inerte.
     */
    private function applyCompanyFilter($query, ?int $companyId)
    {
        if ($companyId) {
            $query->where(function ($q) use ($companyId) {
                $q->where('company_id', $companyId)
                  ->orWhereNull('company_id');
            });
        }

        return $query;
    }

    /**
     * Filtra notificações cujo expires_at já passou (lê do meta JSON nativo,
     * com fallback para `data` legado).
     */
    private function excludeExpiredNotifications($query)
    {
        $now = Carbon::now()->toIso8601String();

        return $query->where(function ($q) use ($now) {
            $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(COALESCE(meta, data), '$.expires_at')) IS NULL")
              ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(COALESCE(meta, data), '$.expires_at')) >= ?", [$now]);
        });
    }

    /**
     * Conta não-lidas com os mesmos filtros aplicados.
     */
    private function getUnreadCount(?int $companyId): int
    {
        $query = Auth::user()->unreadNotifications();
        $query = $this->applyCompanyFilter($query, $companyId);
        $query = $this->excludeExpiredNotifications($query);
        return $query->count();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = session('active_company_id');

        $query = $this->applyCompanyFilter($user->notifications(), $companyId);
        $query = $this->excludeExpiredNotifications($query);
        $notifications = $query->latest()->take(20)->get();

        NotificationResource::primeTriggeredByCache($notifications);

        return response()->json([
            'success' => true,
            'notifications' => NotificationResource::collection($notifications),
            'unread_count' => $this->getUnreadCount($companyId),
        ]);
    }

    public function unreadCount()
    {
        $companyId = session('active_company_id');

        return response()->json([
            'success' => true,
            'unread_count' => $this->getUnreadCount($companyId),
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = \App\Models\AppNotification::find($id);

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notificação não encontrada',
            ], 404);
        }

        // Centraliza autorização na NotificationPolicy::update — bloqueia
        // qualquer tentativa de marcar notificação alheia como lida.
        $this->authorize('update', $notification);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notificação marcada como lida',
        ]);
    }

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

    public function destroy($id)
    {
        $notification = \App\Models\AppNotification::find($id);

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notificação não encontrada',
            ], 404);
        }

        $this->authorize('delete', $notification);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificação removida',
        ]);
    }

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
        $paginated = $query->latest()->paginate(20, ['*'], 'page', $page);

        NotificationResource::primeTriggeredByCache($paginated->items());

        return response()->json([
            'success'       => true,
            'notifications' => NotificationResource::collection($paginated->items()),
            'unread_count'  => $this->getUnreadCount($companyId),
            'pagination'    => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'total'        => $paginated->total(),
                'has_more'     => $paginated->hasMorePages(),
            ],
        ]);
    }

    public function page(Request $request)
    {
        $companyId = session('active_company_id');
        $query = $this->applyCompanyFilter(Auth::user()->notifications(), $companyId);
        $query = $this->excludeExpiredNotifications($query);
        $notifications = $query->latest()->paginate(20);

        return view('app.notifications.index', compact('notifications'));
    }
}
