<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider\Data;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\Notification;
use App\Provider\NotificationProvider;
use App\Provider\ProviderInterface as ProviderInterface_;
use App\Provider\ProviderInterface as UserProviderInterface;
use App\Provider\UserProvider;
use MongoDB\BSON\ObjectId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class NotificationDataProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: NotificationProvider::class)]
        private ProviderInterface_ $notificationProvider,
        #[Autowire(service: UserProvider::class)]
        private ProviderInterface_ $userProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();
        if ($resourceClass !== Notification::class) {
            throw new \RuntimeException(\sprintf('Unsupported resource class: %s', $resourceClass));
        }
        if (isset($uriVariables['id'])) {
            return $this->notificationProvider->getByEntityId((int)$uriVariables['id']);
        } else {
            $criteria = $context['filters'] ?? [];
            if (isset($criteria['userId'])) {
                $user = $this->userProvider->getByEntityId((int)$criteria['userId']) ? : null;

                if (!$user) {
                    return [];
                }

                unset($criteria['userId']);
                $criteria['user.$id'] = new ObjectId($user->getId());
            }
            return !empty($criteria)
                ? $this->notificationProvider->getAllOrBy($criteria)
                : $this->notificationProvider->getAllOrBy();
        }
    }
}