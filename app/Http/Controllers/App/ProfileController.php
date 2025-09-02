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
        // Preencher os dados do usuário com os valores validados
        $user = FacadesAuth::user();
        $user->fill($request->validated());

        // Se o e-mail foi modificado, redefina a verificação do e-mail
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Processar e salvar o avatar se um novo arquivo for enviado
        if ($request->hasFile('avatar')) {
            if ($user->avatar && $user->avatar !== 'tenant/blank.png') {
                // Deletar o avatar anterior do armazenamento
                Storage::delete($user->avatar);
            }

            // Obtém o arquivo de avatar do request
            $avatar = $request->file('avatar');

            // Gera um nome único para o arquivo de avatar
            $avatarName = time() . '_' . $avatar->getClientOriginalName();

            // Salva o arquivo na pasta 'perfis' dentro da pasta de armazenamento (storage/app/public)
            $avatarPath = Storage::putFileAs('perfis', $avatar, $avatarName);

            // Salva o caminho do arquivo na coluna 'avatar' do usuário
            $user->avatar = $avatarPath;
        }

        // Salvar as mudanças no modelo do usuário
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
