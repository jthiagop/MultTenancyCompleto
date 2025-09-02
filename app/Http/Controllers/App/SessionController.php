<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
            'sessions' => $formattedSessions,
            'loginSessions' => $loginSessions
        ]);
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

    /**
     * Define a empresa ativa na sessão do usuário.
     */
    public function switchCompany(Company $company)
    {
        // 1. Pega o usuário autenticado
        $user = Auth::user();

        // 2. VERIFICAÇÃO DE SEGURANÇA: Checa se o usuário realmente tem acesso a essa empresa
        if ($user->companies()->where('id', $company->id)->exists()) {

            // 3. Se tiver acesso, armazena o ID da empresa na sessão
            session(['active_company_id' => $company->id]);
            session()->flash('info', "Você agora está trabalhando na empresa: {$company->name}");
        } else {
            // 4. Se não tiver acesso, retorna um erro ou redireciona com uma mensagem
            session()->flash('error', 'Você não tem permissão para acessar esta empresa.');
            abort(403, 'Acesso não autorizado a esta empresa.');
        }

        // 5. Redireciona para o dashboard ou para a página anterior
        return redirect()->route('dashboard'); // Ou outra rota de sua preferência
    }

    /**
     * Cria uma instância do Agent para uma sessão.
     */
    protected function createAgent($session)
    {
        $agent = new Agent();
        if ($session->user_agent) {
            $agent->setUserAgent($session->user_agent);
        }
        return $agent;
    }

    /**
     * Faz logout de uma sessão específica.
     */
    public function destroy(Request $request, $sessionId)
    {
        // Apenas permite que o usuário deslogue de outras sessões, não da atual
        if ($sessionId === $request->session()->getId()) {
            return back()->with('error', 'Você não pode sair da sua sessão atual.');
        }

        DB::table('sessions')->where('id', $sessionId)->delete();

        return back()->with('success', 'Sessão encerrada com sucesso.');
    }
}
