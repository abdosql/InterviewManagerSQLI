<?php
namespace App\Provider\Data;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\Interview;
use App\Provider\InterviewProvider;
use App\Provider\ProviderInterface as ProviderInterface_;
use App\Provider\UserProvider;
use MongoDB\BSON\ObjectId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Provider\ProviderInterface as InterviewProviderInterface;

readonly class InterviewDataProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: InterviewProvider::class)]
        private InterviewProviderInterface $interviewProvider,
        #[Autowire(service: UserProvider::class)]
        private ProviderInterface_ $userProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();
        if ($resourceClass !== Interview::class) {
            throw new \RuntimeException(\sprintf('Unsupported resource class: %s', $resourceClass));
        }

        $criteria = $context['filters'] ?? [];
        $uri = strtolower($context["uri"] ?? '');

        if ($this->isUpcomingInterviewsRequest($uri)) {
            return $this->handleUpcomingInterviews($criteria);
        }

        if ($this->isFetchingSpecificInterview($uriVariables)) {
            return $this->handleSpecificInterview($uriVariables['id']);
        }

        return $this->handleGeneralCriteria($criteria);
    }

    private function isUpcomingInterviewsRequest(string $uri): bool
    {
        return str_contains($uri, 'upcoming');
    }

    private function handleUpcomingInterviews(array $criteria): array
    {
        if (isset($criteria['userId'])) {
            $user = $this->getUserById($criteria['userId']);
            return $user ? $this->interviewProvider->getUpcomingInterviews(new ObjectId($user->getId())) : [];
        }

        return $this->interviewProvider->getUpcomingInterviews();
    }

    private function isFetchingSpecificInterview(array $uriVariables): bool
    {
        return isset($uriVariables['id']);
    }

    private function handleSpecificInterview(int $entityId): ?Interview
    {
        return $this->interviewProvider->getByEntityId($entityId);
    }

    private function handleGeneralCriteria(array $criteria): array
    {
        if (isset($criteria['userId'])) {
            $user = $this->getUserById($criteria['userId']);
            if (!$user) {
                return [];
            }

            unset($criteria['userId']);
            $criteria['evaluators.$id'] = new ObjectId($user->getId());
        }

        return $this->interviewProvider->getAllOrBy($criteria);
    }

    private function getUserById(int $userId): ?object
    {
        return $this->userProvider->getByEntityId($userId) ?: null;
    }
}
