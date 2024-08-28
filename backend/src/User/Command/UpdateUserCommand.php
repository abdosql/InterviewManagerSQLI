<?php

namespace App\User\Command;

use App\Appreciation\Command\AbstractCommand;
use App\Entity\User;
use App\Message\User\UserUpdatedMessage;
use App\Services\Impl\UserService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class UpdateUserCommand extends AbstractCommand
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
     * @return int
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $this->userService->updateEntity($this->user);
        $message = new UserUpdatedMessage($this->user->getId());
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $this->user->getId();
    }
}