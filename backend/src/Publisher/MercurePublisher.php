<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Publisher;

use App\Entity\User;
use App\Notification\Command\Handler\DefaultCommandHandler;
use App\Notification\Command\SendNotificationCommand;
use App\Services\Impl\NotificationService;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;

class MercurePublisher extends AbstractPublisher
{
    public function __construct(
        private readonly HubInterface          $hub,
        private readonly DefaultCommandHandler $notificationHandler,
        private readonly notificationService   $notificationService,
        protected MessageBusInterface          $messageBus,
    )
    {
        parent::__construct($messageBus);
    }

    /**
     * @throws \Exception
     */
    public function publish(array $data, ?User $user): void
    {
        $update = new Update(
            'user_'.$user->getId(),
            json_encode($data),
            private: true
        );

        $notification = $this->createNotificationInstance($data, $user);

        $command = new SendNotificationCommand($notification, $this->notificationService, $this->messageBus);

        $this->notificationHandler->handle($command);
        $this->hub->publish($update);


    }


}