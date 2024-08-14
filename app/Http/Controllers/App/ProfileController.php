<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('app.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Preencher os dados do usuário com os valores validados
        $user = $request->user();
        $user->fill($request->validated());

        // Se o e-mail foi modificado, redefina a verificação do e-mail
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Processar e salvar o avatar
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
            $avatarPath = Storage::put('perfis', $avatar);

            // Salva o caminho do arquivo na coluna 'avatar' do usuário
            $user->avatar = $avatarPath;
        } else {
            // Define uma imagem padrão caso nenhum arquivo tenha sido enviado
            $user->avatar = 'tenant/blank.png';
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
}
