<?php

namespace App\Command\User;

use App\Command\Abstract\AbstractCommand;
use App\Entity\User;
use App\Message\User\UserUpdatedMessage;
use App\Services\Impl\UserService;

readonly class UpdateUserCommand extends AbstractCommand
{

    public function __construct(private UserService $userService, private User $user)
    {
        parent::__construct($userService);
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
    public static function getMessageClass(): string
    {
        return UserUpdatedMessage::class;
    }
}