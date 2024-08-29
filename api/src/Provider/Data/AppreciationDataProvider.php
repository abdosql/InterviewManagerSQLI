<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider\Data;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\Appreciation;
use App\Provider\AppreciationProvider;
use App\Provider\InterviewProvider;
use App\Provider\ProviderInterface as ProviderInterface_;
use MongoDB\BSON\ObjectId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class AppreciationDataProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: InterviewProvider::class)]
        private ProviderInterface_ $interviewProvider,
        #[Autowire(service: AppreciationProvider::class)]
        private ProviderInterface_ $appreciationProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();
        if ($resourceClass !== Appreciation::class) {
            throw new \RuntimeException(\sprintf('Unsupported resource class: %s', $resourceClass));
        }

        if (isset($uriVariables['id'])) {
            return $this->appreciationProvider->getByEntityId((int)$uriVariables['id']);
        } else {
            $criteria = $context['filters'] ?? [];
            if (isset($criteria['interviewId'])) {
                $interview = $this->interviewProvider->getByEntityId((int)$criteria['interviewId']) ?: null;

                if (!$interview) {
                    return [];
                }

                unset($criteria['interviewId']);
                $criteria['interview.$id'] = new ObjectId($interview->getId());
            }
            return !empty($criteria)
                ? $this->appreciationProvider->getAllOrBy($criteria)
                : $this->appreciationProvider->getAllOrBy();
        }
    }
}