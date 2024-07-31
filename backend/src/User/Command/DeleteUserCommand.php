<?php

namespace App\User\Command;

use App\Candidate\Command\Abstract\AbstractCommand;
use App\Entity\User;
use App\Message\Candidate\CandidateDeletedMessage;
use App\Services\Impl\UserService;

readonly class DeleteUserCommand extends AbstractCommand
{

    public function __construct(private User $user, private UserService $userService)
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
        $evaluatorIdBackup = $this->user->getId();
        $this->userService->deleteEntity($this->user->getId());
        return $evaluatorIdBackup;
    }

    public static function getMessageClass(): string
    {
        return CandidateDeletedMessage::class;
    }
}