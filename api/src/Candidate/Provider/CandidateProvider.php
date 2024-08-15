<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Provider;

use App\Document\Candidate;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;

readonly class CandidateProvider implements ProviderInterface
{
    public function __construct
    (
        private DocumentManager $documentManager
    )
    {
    }

    /**
     * @throws MappingException
     * @throws LockException
     */
    public function getByEntityId(int $entityId): Candidate
    {
        return $this->documentManager->getRepository(Candidate::class)->find($entityId);
    }

    public function getAll(): array
    {
        return $this->documentManager->getRepository(Candidate::class)->findAll();
    }

    public function getBy(array $criteria): array
    {
        return $this->documentManager->getRepository(Candidate::class)->findBy($criteria);
    }
}