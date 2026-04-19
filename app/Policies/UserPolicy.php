<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Usuário com role 'global' pode fazer tudo.
     * Apenas admin pode gerenciar outros usuários.
     */
    private function isAdmin(User $actor): bool
    {
        return $actor->hasAnyRole(['global', 'admin']);
    }

    /**
     * Somente role 'global' pode atribuir/remover roles de alto nível.
     */
    private function isGlobal(User $actor): bool
    {
        return $actor->hasRole('global');
    }

    /** Listar usuários */
    public function viewAny(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    /** Ver detalhes de um usuário */
    public function view(User $actor, User $target): bool
    {
        return $this->isAdmin($actor) || $actor->id === $target->id;
    }

    /** Criar novo usuário */
    public function create(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    /** Editar dados gerais de um usuário (nome, email, avatar, etc.) */
    public function update(User $actor, User $target): bool
    {
        // Admin pode editar qualquer usuário; usuário comum pode editar apenas a si mesmo
        return $this->isAdmin($actor) || $actor->id === $target->id;
    }

    /** Atribuir/remover roles de um usuário */
    public function updateRoles(User $actor, User $target): bool
    {
        // Apenas admin pode alterar roles
        if (!$this->isAdmin($actor)) {
            return false;
        }

        // Ninguém pode alterar roles do próprio usuário global supremo (primeiro usuário)
        $firstUser = User::orderBy('id', 'asc')->value('id');
        if ($target->id === $firstUser && $actor->id !== $firstUser) {
            return false;
        }

        return true;
    }

    /** Atribuir/remover permissões diretas de um usuário */
    public function updatePermissions(User $actor, User $target): bool
    {
        return $this->updateRoles($actor, $target);
    }

    /** Ativar/desativar conta de um usuário */
    public function updateStatus(User $actor, User $target): bool
    {
        // Não pode desativar a própria conta
        if ($actor->id === $target->id) {
            return false;
        }

        // Não pode desativar o usuário global supremo
        $firstUser = User::orderBy('id', 'asc')->value('id');
        if ($target->id === $firstUser) {
            return false;
        }

        return $this->isAdmin($actor);
    }

    /** Redefinir senha de um usuário */
    public function resetPassword(User $actor, User $target): bool
    {
        return $this->isAdmin($actor);
    }

    /** Excluir usuário */
    public function delete(User $actor, User $target): bool
    {
        if ($actor->id === $target->id) {
            return false;
        }

        return $this->isGlobal($actor);
    }
}
