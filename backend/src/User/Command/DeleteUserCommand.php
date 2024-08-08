<?php

namespace App\User\Command;

use App\Candidate\Command\AbstractCommand;
use App\Entity\User;
use App\Message\User\UserDeletedMessage;
use App\Services\Impl\UserService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteUserCommand extends AbstractCommand
{

    public function __construct(
        private User $user,
        private UserService $userService,
        private MessageBusInterface $messageBus,
    )
    {
        parent::__construct($userService, $messageBus);
    }

    /**
     * @throws \Exception
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $evaluatorIdBackup = $this->user->getId();
        $this->userService->deleteEntity($evaluatorIdBackup);
        $message = new UserDeletedMessage($evaluatorIdBackup);
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $evaluatorIdBackup;
    }

}