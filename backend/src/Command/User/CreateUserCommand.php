<?php

namespace App\Command\User;

use App\Command\Abstract\AbstractCommand;
use App\Entity\User;
use App\Manager\UserCredentialManager;
use App\Message\User\UserCreatedMessage;
use App\Services\Impl\UserService;

readonly class CreateUserCommand extends AbstractCommand
{

    public function __construct(private UserService $userService, private User $user,private UserCredentialManager $credentialManager)
    {
        parent::__construct($userService);
    }

    /**
     * @return int
     */
    public function execute(): int
    {
        $credentialManager = $this->credentialManager->generateCredentials($this->user);
        $this->credentialManager->applyCredentialsToUser($this->user, $credentialManager);
        $this->userService->saveEntity($this->user);
        return $this->user->getId();
    }

    public static function getMessageClass(): string
    {
        return UserCreatedMessage::class;
    }
}