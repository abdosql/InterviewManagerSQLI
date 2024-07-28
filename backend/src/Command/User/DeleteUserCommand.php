<?php

namespace App\Command\User;

use App\Entity\Candidate;
use App\Entity\Evaluator;
use App\Entity\User;
use App\Services\Impl\UserService;

class DeleteUserCommand
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
        $evaluatorIdBackup = $this->user->getId();
        $this->userService->deleteEntity($this->user->getId());
        return $evaluatorIdBackup;
    }
}