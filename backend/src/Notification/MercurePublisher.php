<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Notification;

use App\Entity\Candidate;
use App\Entity\Notification;
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
        // Publish to Mercure
        $update = new Update(
            'user_'.$user->getId(),
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
}