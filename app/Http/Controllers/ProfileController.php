<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

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

        return view('profile.edit', ['sessions' => $formattedSessions]);
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
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
