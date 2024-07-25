<?php

namespace App\Command\UserCommands;

use App\Command\CommandInterface;
use App\Entity\User;
use App\Services\Impl\UserService;

class UpdateUserCommand implements CommandInterface
{
    protected User $user;
    private UserService $userService;
    public function __construct(User $user, UserService $userService) {
        $this->user = $user;
        $this->userService = $userService;
    }

    /**
     * @throws \Exception
     */
    public function execute(): int
    {
        if ($this->user->getId() == null) {
            throw new \Exception('User ID is required');
        }
        $this->userService->updateEntity($this->user);
        return $this->user->getId();
    }
}