<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Publisher;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercurePublisher implements PublisherInterface
{
    public function __construct(private HubInterface $hub, private EntityManagerInterface $entityManager)
    {
    }

    public function publish(array $data, ?User $user): void
    {
        $update = new Update(
            'user_3',
            json_encode($data)
            , private: true
        );
        $this->hub->publish($update);

//        $notification = new Notification();
//        $notification->setContent(json_encode($data));
//        $notification->setNotificationDate(new \DateTime());
//        $notification->setUser($user);
//
//        $this->entityManager->persist($notification);
//        $this->entityManager->flush();
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
}