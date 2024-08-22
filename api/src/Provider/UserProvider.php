<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;

readonly class UserProvider extends AbstractProvider implements ProviderInterface
{
    public function __construct(DocumentManager $documentManager)
    {
        parent::__construct($documentManager);
    }

    public function getByEntityId(int $entityId): User
    {
        return $this->documentManager->getRepository(User::class)->findOneBy(["entityId" => $entityId]);
    }

    public function getAllOrBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return
            is_array($criteria) ? $this->documentManager
                ->getRepository(User::class)
                ->findBy($criteria, $orderBy, $limit, $offset)
                :
                $this->documentManager
                    ->getRepository(User::class)
                    ->findAll()
            ;

    }

    public function getAllByIds(array $ids): array
    {
        return $this->documentManager
            ->getRepository(User::class)
            ->findBy(['entityId' => ['$in' => $ids]]);
    }
}