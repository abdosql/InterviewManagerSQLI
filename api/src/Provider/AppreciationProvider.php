<?php

namespace App\Provider;

use App\Document\Appreciation;
use App\Document\Interview;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

readonly class AppreciationProvider extends AbstractProvider implements ProviderInterface
{
    public function __construct(DocumentManager $documentManager)
    {
        parent::__construct($documentManager);
    }

    public function getByEntityId(int $entityId): ?Appreciation
    {
        return $this->documentManager->getRepository(Appreciation::class)->findOneBy(["entityId" => $entityId]);
    }

    public function getAllOrBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return
            is_array($criteria) ? $this->documentManager
                ->getRepository(Appreciation::class)
                ->findBy($criteria, $orderBy, $limit, $offset)
                :
                $this->documentManager
                    ->getRepository(Appreciation::class)
                    ->findAll()
            ;
    }


}