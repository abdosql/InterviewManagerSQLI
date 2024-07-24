<?php

namespace App\Command\UserCommands;

use App\Command\CommandInterface;
use App\Entity\Evaluator;
use App\Entity\User;
use App\Services\Impl\EvaluatorService;
use App\Services\Impl\UserService;
use App\Services\Manager\UserCredentialManager;

class CreateUserCommand implements CommandInterface
{
    private User $user;
    private UserService $userService;
    private UserCredentialManager $credentialManager;
    public function __construct(User $evaluator, UserService $evaluatorService, UserCredentialManager $credentialManager)
    {
        $this->user = $evaluator;
        $this->userService = $evaluatorService;
        $this->credentialManager = $credentialManager;
    }

    public function execute(): int
    {
        $credentialManager = $this->credentialManager->generateCredentials($this->user);
        $this->credentialManager->applyCredentialsToUser($this->user, $credentialManager);
        $this->userService->saveEntity($this->user);
        return $this->user->getId();
    }
}