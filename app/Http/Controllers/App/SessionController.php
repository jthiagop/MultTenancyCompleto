<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sessions = DB::table('sessions')->get();

        // Formatando os dados para a visualização
        $formattedSessions = $sessions->map(function ($session) {
            return [
                'local' => 'Australia', // Aqui você pode substituir pelo local real, se disponível
                'dispositivo' => $this->getDeviceFromUserAgent($session->user_agent),
                'ip_address' => $session->ip_address,
                'hora' => $this->formatLastActivity($session->last_activity),
                'acoes' => $this->getActions($session->last_activity),
            ];
        });

        return view('app.profile.edit', ['sessions' => $formattedSessions]);
    }

    private function getDeviceFromUserAgent($userAgent)
    {
        // Lógica para extrair informações do dispositivo a partir do user agent
        // Exemplo simples
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome - Windows';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari - iOS';
        }
        return 'Unknown';
    }

    private function formatLastActivity($lastActivity)
    {
        $now = Carbon::now();
        $lastActivity = Carbon::createFromTimestamp($lastActivity);

        return $lastActivity->diffInSeconds($now) < 60 ?
            '23 seconds ago' :
            ($lastActivity->diffInDays($now) == 0 ?
            $lastActivity->diffInHours($now) . ' hours ago' :
            $lastActivity->diffInDays($now) . ' days ago');
    }

    private function getActions($lastActivity)
    {
        $now = Carbon::now();
        $lastActivity = Carbon::createFromTimestamp($lastActivity);

        if ($lastActivity->diffInDays($now) < 1) {
            return 'Current session';
        } elseif ($lastActivity->diffInDays($now) < 7) {
            return '<a href="#" data-kt-users-sign-out="single_user">Sign out</a>';
        } else {
            return 'Expired';
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
