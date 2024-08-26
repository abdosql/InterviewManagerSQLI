<?php

namespace App\User\Command;

use App\Candidate\Command\AbstractCommand;
use App\Entity\Evaluator;
use App\Entity\HRManager;
use App\Entity\User;
use App\Manager\UserCredentialManager;
use App\Message\User\UserCreatedMessage;
use App\Services\Impl\UserService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateUserCommand extends AbstractCommand
{

    public function __construct(
        private User $user,
        private UserService $userService,
        private UserCredentialManager $credentialManager,
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
        $credentialManager = $this->credentialManager->generateCredentials($this->user);
        if ($this->user instanceof Evaluator){
            $this->user->setRoles(['ROLE_EVALUATOR', 'PUBLIC_ACCESS']);
        }
        if ($this->user instanceof HRManager){
            $this->user->setRoles(['ROLE_HR', 'PUBLIC_ACCESS']);
        }
        $this->credentialManager->applyCredentialsToUser($this->user, $credentialManager);
        $this->userService->saveEntity($this->user);
        $message = new UserCreatedMessage($this->user->getId());
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $this->user->getId();
    }

}