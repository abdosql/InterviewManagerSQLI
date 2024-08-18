<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Interview\Command;

use App\Interview\Command\AbstractCommand;
use App\Entity\Interview;
use App\Message\Interview\InterviewCreatedMessage;
use App\Services\Impl\InterviewService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateInterviewCommand extends AbstractCommand
{
    public function __construct(
        private Interview $interview,
        private InterviewService $interviewService,
        private MessageBusInterface $messageBus,

    )
    {
        parent::__construct($interviewService, $this->messageBus);
    }

    /**
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $this->interviewService->saveEntity($this->interview);
        $message = new InterviewCreatedMessage($this->interview->getId());
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $this->interview->getId();
    }
}