<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Risk;

class RiskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('superadmin')
            || $user->hasRole('staf')
            || $user->hasRole('supervisor')
            || $user->hasRole('manajer')
            || $user->hasRole('direksi');
    }

    public function view(User $user, Risk $risk): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        // staff boleh input risiko, role lain juga boleh
        return $user->hasRole('superadmin')
            || $user->hasRole('staf')
            || $user->hasRole('supervisor')
            || $user->hasRole('manajer')
            || $user->hasRole('direksi');
    }

    public function update(User $user, Risk $risk): bool
{
    // staff hanya boleh edit miliknya sendiri
    if ($user->hasRole('staf')) {
        return $risk->dibuat_oleh === $user->id;
    }

    return $user->hasRole('superadmin')
        || $user->hasRole('supervisor')
        || $user->hasRole('manajer')
        || $user->hasRole('direksi');
}

    public function delete(User $user, Risk $risk): bool
    {
        return $user->hasRole('superadmin');
    }

    public function approve(User $user, Risk $risk): bool
    {
        return $user->hasRole('supervisor')
            || $user->hasRole('manajer')
            || $user->hasRole('direksi');
    }

        public function review(User $user, Risk $risk): bool
    {
        // hanya supervisor, manajer, atau direksi yang boleh meninjau
        return $user->hasRole('supervisor')
            || $user->hasRole('manajer')
            || $user->hasRole('direksi');
    }
}

