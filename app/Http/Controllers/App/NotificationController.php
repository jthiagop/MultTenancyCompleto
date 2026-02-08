<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Retorna as notificações do usuário logado.
     * Filtra por company_id se disponível.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = session('active_company_id');

        $query = $user->notifications();

        // Filtrar por company_id se estiver no payload
        if ($companyId) {
            $query->where(function ($q) use ($companyId) {
                $q->whereJsonContains('data->company_id', $companyId)
                  ->orWhereNull('data->company_id');
            });
        }

        $notifications = $query->latest()->take(20)->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'icon' => $notification->data['icon'] ?? 'ki-notification',
                'color' => $notification->data['color'] ?? 'primary',
                'title' => $notification->data['title'] ?? 'Notificação',
                'message' => $notification->data['message'] ?? '',
                'action_url' => $notification->data['action_url'] ?? null,
                'target' => $notification->data['target'] ?? '_self',
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans(),
                'created_at_iso' => $notification->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()
                ->when($companyId, function ($q) use ($companyId) {
                    $q->where(function ($q2) use ($companyId) {
                        $q2->whereJsonContains('data->company_id', $companyId)
                           ->orWhereNull('data->company_id');
                    });
                })
                ->count(),
        ]);
    }

    /**
     * Retorna apenas a contagem de notificações não lidas.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $companyId = session('active_company_id');

        $count = $user->unreadNotifications()
            ->when($companyId, function ($q) use ($companyId) {
                $q->where(function ($q2) use ($companyId) {
                    $q2->whereJsonContains('data->company_id', $companyId)
                       ->orWhereNull('data->company_id');
                });
            })
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }

    /**
     * Marca uma notificação como lida.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

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
        $user = Auth::user();
        $companyId = session('active_company_id');

        $query = $user->unreadNotifications();

        if ($companyId) {
            $query->where(function ($q) use ($companyId) {
                $q->whereJsonContains('data->company_id', $companyId)
                  ->orWhereNull('data->company_id');
            });
        }

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
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

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
        $user = Auth::user();
        $companyId = session('active_company_id');

        $query = $user->readNotifications();

        if ($companyId) {
            $query->where(function ($q) use ($companyId) {
                $q->whereJsonContains('data->company_id', $companyId)
                  ->orWhereNull('data->company_id');
            });
        }

        $deleted = $query->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deleted} notificações removidas",
        ]);
    }

    /**
     * Exibe a página completa de notificações.
     */
    public function page(Request $request)
    {
        $user = Auth::user();
        $companyId = session('active_company_id');

        $query = $user->notifications();

        // Filtrar por company_id se estiver no payload
        if ($companyId) {
            $query->where(function ($q) use ($companyId) {
                $q->whereJsonContains('data->company_id', $companyId)
                  ->orWhereNull('data->company_id');
            });
        }

        // Paginação
        $notifications = $query->latest()->paginate(20);

        return view('app.notifications.index', compact('notifications'));
    }
}
