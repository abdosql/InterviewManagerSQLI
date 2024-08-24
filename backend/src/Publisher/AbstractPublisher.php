<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Publisher;

use App\Entity\Notification;
use App\Entity\User;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class AbstractPublisher implements PublisherInterface
{
    public function __construct(
        protected MessageBusInterface $messageBus,

    )
    {
    }
    public function publishToMultipleUsers(array $data, array $users): void
    {
        foreach ($users as $user){
            if (!$user instanceof User){
                throw new \InvalidArgumentException($data["title"]);
            }

            $this->publish($data, $user);
        }
    }

    protected function createNotificationInstance(array $data, User $user): Notification
    {
        $notification = new Notification();
        $notification
            ->setContent($data["message"])
            ->setNotificationDate($data["date"])
            ->setLink($data["url"])
            ->setRead(false)
            ->setUser($user);
        return $notification;
    }
}