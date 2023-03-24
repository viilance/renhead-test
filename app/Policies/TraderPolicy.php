<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TraderPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function userCanAuth(User $user): bool
    {
        return $user->getAttribute('type') === 'Approver';
    }
}
