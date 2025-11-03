<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Simulation;

class SimulationPolicy
{
    public function view(User $user, Simulation $simulation): bool
    {
        return $user->id === $simulation->user_id;
    }

    public function update(User $user, Simulation $simulation): bool
    {
        return $user->id === $simulation->user_id;
    }

    public function delete(User $user, Simulation $simulation): bool
    {
        return $user->id === $simulation->user_id;
    }
}