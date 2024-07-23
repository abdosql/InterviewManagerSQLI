<?php
declare(strict_types=1);

namespace App\Services\Impl;

use App\Document\EvaluatorDocument;
use App\Entity\Evaluator;
use App\Services\DatabasePersistence\DocumentPersistenceServiceInterface;
use App\Services\DatabasePersistence\EntityPersistenceServiceInterface;
use App\Services\Impl\Abstract\AbstractUserService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\EntityManagerInterface;

class EvaluatorService extends AbstractUserService
{

}