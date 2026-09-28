<?php

namespace App\Services;

use App\Models\Entity\User;

class UserService
{
    public function find(User $user): User
    {
        return $user;
    }
}
