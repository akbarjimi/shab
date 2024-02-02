<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create orders.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can retrieve orders.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function retrieve(User $user)
    {
        return true;
    }
}
