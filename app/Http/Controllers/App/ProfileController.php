<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
    public function update(Request $request, User $user)
    {
        dd($request->all());
        // Validação dos dados
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Exemplo de regras para o campo avatar

        ]);

        dd($validatedData);

       $user->update($validatedData);

        // Processar e salvar o avatar
        if ($request->hasFile('avatar')) {
            // Obtém o arquivo de avatar do request
            $avatar = $request->file('avatar');

            // Gera um nome único para o arquivo de avatar
            $avatarName = time() . '_' . $avatar->getClientOriginalName();

            // Salva o arquivo na pasta 'perfis' dentro da pasta de armazenamento (storage/app/public)
            $avatarPath = Storage::put('perfis', $request->file('avatar'));

            // Salva o nome do arquivo na coluna 'avatar' do usuário no banco de dados
            $validatedData['avatar'] = $avatarPath;
        } else {
            // Define uma imagem padrão caso nenhum arquivo tenha sido enviado
            $validatedData['avatar'] = 'tenant/blank.png'; // Ajuste o caminho conforme necessário
        }

        // Sincronização de roles
        if (isset($validatedData['roles'])) {
            $user->roles()->sync($validatedData['roles']);
        }

        // Sincronização de filiais (se necessário)
        if (isset($validatedData['filiais'])) {
            $user->filiais()->sync($validatedData['filiais']);
        }

        // Verificação de alteração de email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Salvar as alterações
        $user->save();

        // Redirecionar ou retornar uma resposta
        return redirect()->route('profile.edit', $user->id)->with('success', 'Perfil atualizado com sucesso!');
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
