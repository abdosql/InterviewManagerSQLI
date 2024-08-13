<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Interview\Command;

use App\Entity\Interview;
use App\Message\Interview\InterviewDeletedMessage;
use App\Services\Impl\InterviewService;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class DeleteInterviewCommand extends AbstractCommand
{

    public function __construct(private Interview $interview,
                                private InterviewService $interviewService,
                                private MessageBusInterface $messageBus,
    )
    {
        parent::__construct($this->interviewService, $this->messageBus);
    }

    /**
     * @return int
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $interviewIdBackup = $this->interview->getId();
        $this->interviewService->deleteEntity($this->interview->getId());
        $message = new InterviewDeletedMessage($interviewIdBackup);
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $interviewIdBackup;
    }
}