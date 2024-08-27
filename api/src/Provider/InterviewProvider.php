<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider;

use App\Document\Interview;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;

readonly class InterviewProvider extends AbstractProvider implements ProviderInterface
{


    public function __construct(DocumentManager $documentManager)
    {
        parent::__construct($documentManager);
    }

    public function getByEntityId(int $entityId): Interview
    {
        return $this->documentManager->getRepository(Interview::class)->findOneBy(["entityId" => $entityId]);
    }

    public function getAllOrBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        return
            is_array($criteria) ? $this->documentManager
                ->getRepository(Interview::class)
                ->findBy($criteria, $orderBy, $limit, $offset)
                :
                $this->documentManager
                    ->getRepository(Interview::class)
                    ->findAll()
            ;

    }

    public function getUpcomingInterviews(?ObjectId $userId = null): array
    {
        $criteria = ['interviewDate' => ['$gte' => new \DateTime()]];
        if ($userId) {
            $criteria['evaluators.$id'] = $userId;
        }

        return $this->getAllOrBy($criteria, ['interviewDate' => 'ASC']);
    }
//    public function getUpcomingInterviews(): array
//    {
//        return $this->documentManager
//            ->getRepository(Interview::class)
//            ->findUpcomingEvents();
//    }
}