<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider\Data;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\User;
use App\Provider\ProviderInterface as UserProviderInterface;
use App\Provider\UserProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class UserDataProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: UserProvider::class)]
        private UserProviderInterface $userProvider
    )
    {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();
        if ($resourceClass !== User::class) {
            throw new \RuntimeException(\sprintf('Unsupported resource class: %s', $resourceClass));
        }
        if (isset($uriVariables['id']) && is_int($uriVariables['id'])) {
            return $this->userProvider->getByEntityId($uriVariables['id']);
        } else {
            $criteria = $context['filters'] ?? [];
            if (isset($context['filters']['roles'])) {
                $criteria['roles'] = $context['filters']['roles'];
            }
            if (isset($criteria['ids'])) {
                $ids = $context['filters']['ids'];
                if (is_string($ids)) {
                    $ids = explode(',', $ids);
                } elseif (!is_array($ids)) {
                    throw new \InvalidArgumentException('The "ids" parameter must be an array or a comma-separated string.');
                }
                return $this->userProvider->getAllByIds($ids);            }

            return !empty($criteria)
                ? $this->userProvider->getAllOrBy($criteria)
                : $this->userProvider->getAllOrBy();
        }
    }
}