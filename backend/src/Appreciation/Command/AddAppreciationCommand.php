<?php

namespace App\Appreciation\Command;

use App\Entity\Appreciation;
use App\Message\Appreciation\AppreciationAddMessage;
use App\Message\Candidate\CandidateCreatedMessage;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class AddAppreciationCommand extends AbstractCommand
{
    public function __construct(private Appreciation $appreciation,
                                private EntityPersistenceServiceInterface $appreciationService,
                                private MessageBusInterface $messageBus,
    )
    {
        parent::__construct($appreciationService, $messageBus);
    }

    /**
     * @return mixed
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $this->appreciationService->saveEntity($this->appreciation);
        $message = new AppreciationAddMessage($this->appreciation->getId());
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $this->appreciation->getId();
    }
}