<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider\Data;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\Interview;
use App\Provider\InterviewProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\Provider\ProviderInterface as InterviewProviderInterface;

readonly class InterviewDataProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: InterviewProvider::class)]
        private InterviewProviderInterface $interviewProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();
        if ($resourceClass !== Interview::class) {
            throw new \RuntimeException(\sprintf('Unsupported resource class: %s', $resourceClass));
        }

        if (str_contains(strtolower($context["uri"]), 'upcoming')) {
            return $this->interviewProvider->getUpcomingInterviews();
        }
        if (isset($uriVariables['id'])) {
            return $this->interviewProvider->getByEntityId((int)$uriVariables['id']);
        } else {
            $criteria = $context['filters'] ?? [];
            return !empty($criteria)
                ? $this->interviewProvider->getAllOrBy($criteria)
                : $this->interviewProvider->getAllOrBy();
        }
    }
}