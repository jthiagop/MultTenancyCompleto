<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // Pega todas as sessões do banco de dados
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::user()->id)
            ->latest('last_activity')
            ->get();

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

        // Mapeia os dados para a view, processando cada sessão
        $loginSessions = $sessions->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                'id' => $session->id, // Adicionado para a ação de logout
            ];
        });

        return view('app.profile.edit', [
            'user' => $request->user(),
            'sessions' => $formattedSessions,
            'loginSessions' => $loginSessions
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = FacadesAuth::user();

        // Usar apenas name/email do validated — avatar é tratado à parte.
        $user->fill($request->safe()->only(['name', 'email']));

        // Se o e-mail foi modificado, redefina a verificação do e-mail
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Processar e salvar o avatar se um novo arquivo for enviado.
        // Observação: o ProfileUpdateRequest já valida mime/tamanho.
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');

            // Sanitizar nome: gerar nome totalmente novo (sem confiar em getClientOriginalName).
            // Extensão derivada do MIME validado para evitar polyglots.
            $extension = $avatar->extension() ?: $avatar->getClientOriginalExtension();
            $extension = strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $extension)) ?: 'jpg';

            $avatarName = 'u'.$user->id.'_'.time().'_'.bin2hex(random_bytes(8)).'.'.$extension;

            if ($user->avatar && $user->avatar !== 'tenant/blank.png') {
                Storage::delete($user->avatar);
            }

            $avatarPath = Storage::putFileAs('perfis', $avatar, $avatarName);
            $user->avatar = $avatarPath;
        }

        $user->save();

        // Redirecionar para a rota de edição de perfil com uma mensagem de sucesso
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

    protected function createAgent($session)
    {
        $agent = new Agent();
        if ($session->user_agent) {
            $agent->setUserAgent($session->user_agent);
        }
        return $agent;
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
            '23 seconds ago' : ($lastActivity->diffInDays($now) == 0 ?
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
}
