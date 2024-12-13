<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Repository;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Doctrine\ODM\MongoDB\UnitOfWork;

class InterviewRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm, UnitOfWork $uow, ClassMetadata $classMetadata)
    {
        parent::__construct($dm, $uow, $classMetadata);
    }

    /**
     * @throws MongoDBException
     */
    public function findUpcomingEvents(): array
    {
        $qb = $this->createQueryBuilder('i');
        $qb->field('interviewDate')->gt(new \DateTime());
        return $qb->getQuery()->execute()->toArray();
    }
}