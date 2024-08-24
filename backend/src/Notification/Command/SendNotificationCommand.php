<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Notification\Command;

use App\Entity\Notification;
use App\Message\Notification\NotificationCreatedMessage;
use App\Services\Impl\NotificationService;
use App\User\Command\AbstractCommand;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class SendNotificationCommand extends AbstractCommand
{
    public function __construct(
        private notification          $notification,
        private notificationService   $notificationService,
        protected MessageBusInterface $messageBus,

    )
    {
        parent::__construct($notificationService);
    }

    /**
     * @throws ExceptionInterface
     */
    public function execute(): int
    {
        $this->notificationService->saveEntity($this->notification);
        $message = new NotificationCreatedMessage($this->notification->getId());
        try {
            $this->messageBus->dispatch($message);
        }catch (TransportException $e) {
            throw new \RuntimeException('Failed to dispatch '.$message::class." : ". $e->getMessage());
        }
        return $this->notification->getId();
    }
}