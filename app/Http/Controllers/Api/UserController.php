<?php

namespace App\Http\Controllers\Api;

use App\Models\Entity\User;
use App\Services\UserService;

class UserController extends ApiController
{
    public function __construct(private UserService $userService)
    {
    }

    public function show(User $user)
    {
        return $this->sendResponse($this->userService->find($user), 'Data user berhasil diambil.');
    }
}
