<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider\Data;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\Candidate;
use App\Provider\ProviderInterface as CandidateProviderInterface;

readonly class CandidateDataProvider implements ProviderInterface
{

    public function __construct(private CandidateProviderInterface $candidateProvider)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();
        if ($resourceClass !== Candidate::class) {
            throw new \RuntimeException(\sprintf('Unsupported resource class: %s', $resourceClass));
        }

        if (isset($uriVariables['id'])) {
            return $this->candidateProvider->getByEntityId((int)$uriVariables['id']);
        } else {
            $criteria = $context['filters'] ?? [];

            return !empty($criteria)
                ? $this->candidateProvider->getAllOrBy($criteria)
                : $this->candidateProvider->getAllOrBy();
        }
    }
}
