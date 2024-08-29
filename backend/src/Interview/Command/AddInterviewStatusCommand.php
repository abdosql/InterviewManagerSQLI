<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Interview\Command;

use App\Entity\InterviewStatus;
use App\Message\Interview\InterviewStatusAddMessage;
use App\Services\Impl\InterviewStatusService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class AddInterviewStatusCommand extends AbstractCommand
{
    public function __construct(
        private InterviewStatus $interviewStatus,
        private InterviewStatusService $interviewStatusService,
        private MessageBusInterface $messageBus,

    )
    {
        parent::__construct($interviewStatusService, $this->messageBus);
    }

    /**
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $this->interviewStatusService->saveEntity($this->interviewStatus);
        $message = new InterviewStatusAddMessage($this->interviewStatus->getId());
        try {
            $this->messageBus->dispatch($message);

        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $this->interviewStatus->getId();
    }
}