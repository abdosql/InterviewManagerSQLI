<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Provider\Data;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Document\Candidate;

readonly class CandidateDataProvider implements ProviderInterface
{
    public function __construct(private \App\Candidate\Provider\ProviderInterface $candidateProvider)
    {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();

        if ($resourceClass !== Candidate::class) {
            throw new \RuntimeException(sprintf('Unsupported resource class: %s', $resourceClass));
        }

        if (isset($uriVariables['id'])) {
            return $this->candidateProvider->getByEntityId((int)$uriVariables['id']);
        } else {
            $criteria = $context['filters'] ?? [];
            if (!empty($criteria)) {
                return $this->candidateProvider->getBy($criteria);
            } else {
                return $this->candidateProvider->getAll();
            }
        }
    }
}