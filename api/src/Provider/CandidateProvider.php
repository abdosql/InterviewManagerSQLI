<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider;

use App\Document\Candidate;
use Doctrine\ODM\MongoDB\DocumentManager;

readonly class CandidateProvider implements ProviderInterface
{
    public function __construct(private DocumentManager $documentManager)
    {
    }

    public function getByEntityId(int $entityId): Candidate
    {
        return $this->documentManager->getRepository(Candidate::class)->findOneBy(["entityId" => $entityId]);
    }

    public function getAllOrBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return
            !($criteria)
                ?
                $this->documentManager
                    ->getRepository(Candidate::class)
                    ->findBy($criteria, $orderBy, $limit, $offset)
                :
                $this->documentManager->getRepository(Candidate::class)->findAll();
    }
}