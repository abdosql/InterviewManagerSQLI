<?php

namespace App\CommandHandler\CandidateCommandHandlers;

use App\Command\CandidateCommands\CreateCandidateCommand;
use App\Entity\Candidate;
use App\Event\CandidateEvents\CandidateCreatedEvent;
use App\Services\Interfaces\FileUploadServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class CreateCandidateCommandHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
        private FileUploadServiceInterface $resumeUploadService
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(CreateCandidateCommand $command): void
    {
        $candidate = new Candidate();
        $candidate->setFirstName($command->firstName);
        $candidate->setLastName($command->lastName);
        $candidate->setPhone($command->phone);
        $candidate->setEmail($command->email);
        $candidate->setAddress($command->address);
        if ($command->hireDate) {
            $candidate->setHireDate($command->hireDate);
        }
        if ($command->resumeFilePath){
            $candidate->getResume()->setFilePath($command->resumeFilePath);
        }

        $this->entityManager->persist($candidate);
        $this->entityManager->flush();

        $event = new CandidateCreatedEvent(
            $candidate->getId(),
            $candidate->getFirstName(),
            $candidate->getLastName(),
            $candidate->getPhone(),
            $candidate->getEmail(),
            $candidate->getAddress(),
            $candidate->getHireDate(),
            $candidate->getResume()->getFilePath()
        );

        $this->messageBus->dispatch($event);
    }
}