<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Twig;

use App\Notification\Query\ItemsQueryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class NotificationExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ItemsQueryInterface     $getAllNotificationsQuery,
        private readonly Security                $security,
    )
    {
    }

    public function getGlobals(): array
    {
        $user = $this->security->getUser();
        $userId = $user ? $user->getId() : null;
        $notifications = $this->getAllNotificationsQuery->findItems(["userId" => $userId]);
        return [
            'notifications' => $notifications,
            'unreadCount' => $this->getAllNotificationsQuery->unreadCount($notifications)
        ];
    }
}