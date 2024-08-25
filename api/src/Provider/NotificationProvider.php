<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider;

use App\Document\Notification;
use Doctrine\ODM\MongoDB\DocumentManager;

readonly class NotificationProvider extends AbstractProvider implements ProviderInterface
{


    public function __construct(DocumentManager $documentManager)
    {
        parent::__construct($documentManager);
    }

    public function getByEntityId(int $entityId): Notification
    {
        return $this->documentManager->getRepository(Notification::class)->findOneBy(["entityId" => $entityId]);
    }

    public function getAllOrBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return
            is_array($criteria) ? $this->documentManager
                ->getRepository(Notification::class)
                ->findBy($criteria, $orderBy, $limit, $offset)
                :
                $this->documentManager
                    ->getRepository(Notification::class)
                    ->findAll()
            ;

    }
}