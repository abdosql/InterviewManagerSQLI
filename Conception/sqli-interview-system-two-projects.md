# Candidate Management System: Revised CQRS Implementation

## Project Structure

The system is divided into two separate projects, implementing a CQRS (Command Query Responsibility Segregation) architecture:

1. Write Project (MySQL)
2. Read Project (MongoDB)

### 1. Write Project (MySQL)

```
write-project/
├── src/
│   ├── Command/
│   │   └── CandidateCommands/
│   │       └── CreateCandidateCommand.php
│   ├── CommandHandler/
│   │   └── CandidateCommandHandlers/
│   │       └── CreateCandidateCommandHandler.php
│   ├── Entity/
│   │   └── Candidate.php
│   ├── Message/
│   │   └── CandidateMessages/
│   │       └── CandidateCreatedMessage.php
│   ├── MessageHandler/
│   │   └── CandidateMessageHandlers/
│   │       └── CandidateCreatedMessageHandler.php
│   ├── Factory/
│   │   └── DocumentFactory/
│   │       └── CandidateFactory.php
│   └── Controller/
├── config/
│   ├── packages/
│   │   ├── doctrine.yaml
│   │   └── messenger.yaml
│   └── services.yaml
└── .env
```

### 2. Read Project (MongoDB)

```
read-project/
├── src/
│   ├── Document/
│   │   ├── CandidateDocument.php
│   │   └── ResumeDocument.php
│   ├── Repository/
│   │   └── CandidateRepository.php
│   ├── Controller/
│   │   └── CandidateController.php
│   └── Service/
│       └── CandidateService.php
├── config/
│   ├── packages/
│   │   ├── doctrine_mongodb.yaml
│   │   └── api_platform.yaml
│   └── services.yaml
└── .env
```

## Implementation Details

### 1. Write Project (MySQL)

The Write Project remains as you've implemented it. Here's a summary of its key components:

#### Commands

```php
// write-project/src/Command/CandidateCommands/CreateCandidateCommand.php
namespace App\Command\CandidateCommands;

use App\Entity\Candidate;
use Doctrine\ORM\EntityManagerInterface;

readonly class CreateCandidateCommand
{
    // ... (implementation as provided)
}
```

#### Command Handlers

```php
// write-project/src/CommandHandler/CandidateCommandHandlers/CreateCandidateCommandHandler.php
namespace App\CommandHandler\CandidateCommandHandlers;

use App\Command\CandidateCommands\CreateCandidateCommand;
use App\Message\CandidateMessages\CandidateCreatedMessage;
use Symfony\Component\Messenger\MessageBusInterface;

readonly class CreateCandidateCommandHandler
{
    // ... (implementation as provided)
}
```

#### Messages

```php
// write-project/src/Message/CandidateMessages/CandidateCreatedMessage.php
namespace App\Message\CandidateMessages;

readonly class CandidateCreatedMessage
{
    // ... (implementation as provided)
}
```

#### Message Handlers

```php
// write-project/src/MessageHandler/CandidateMessageHandlers/CandidateCreatedMessageHandler.php
namespace App\MessageHandler\CandidateMessageHandlers;

use App\Entity\Candidate;
use App\Factory\DocumentFactory\CandidateFactory;
use App\Message\CandidateMessages\CandidateCreatedMessage;
use App\Document\CandidateDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CandidateCreatedMessageHandler
{
    // ... (implementation as provided)
}
```

#### Factory

```php
// write-project/src/Factory/DocumentFactory/CandidateFactory.php
namespace App\Factory\DocumentFactory;

use App\Document\CandidateDocument;
use App\Document\ResumeDocument;
use App\Entity\Candidate;

class CandidateFactory
{
    // ... (implementation as provided)
}
```

### 2. Read Project (MongoDB)

The Read Project focuses solely on querying MongoDB. Here's how it could be structured:

#### Documents

```php
// read-project/src/Document/CandidateDocument.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class CandidateDocument
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: 'integer')]
    private $entityId;

    #[MongoDB\Field(type: 'string')]
    private $firstName;

    #[MongoDB\Field(type: 'string')]
    private $lastName;

    #[MongoDB\Field(type: 'string')]
    private $phone;

    #[MongoDB\Field(type: 'string')]
    private $email;

    #[MongoDB\Field(type: 'string')]
    private $address;

    #[MongoDB\Field(type: 'date')]
    private $hireDate;

    #[MongoDB\EmbedOne(targetDocument: ResumeDocument::class)]
    private $resume;

    // ... getters and setters
}
```

#### Repository

```php
// read-project/src/Repository/CandidateRepository.php
namespace App\Repository;

use App\Document\CandidateDocument;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class CandidateRepository extends DocumentRepository
{
    public function findAllCandidates()
    {
        return $this->createQueryBuilder()
            ->sort('lastName', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function findCandidateById($id)
    {
        return $this->createQueryBuilder()
            ->field('entityId')->equals($id)
            ->getQuery()
            ->getSingleResult();
    }

    // Add more query methods as needed
}
```

#### Service

```php
// read-project/src/Service/CandidateService.php
namespace App\Service;

use App\Repository\CandidateRepository;

class CandidateService
{
    private $candidateRepository;

    public function __construct(CandidateRepository $candidateRepository)
    {
        $this->candidateRepository = $candidateRepository;
    }

    public function getAllCandidates()
    {
        return $this->candidateRepository->findAllCandidates();
    }

    public function getCandidateById($id)
    {
        return $this->candidateRepository->findCandidateById($id);
    }

    // Add more business logic methods as needed
}
```

#### Controller

```php
// read-project/src/Controller/CandidateController.php
namespace App\Controller;

use App\Service\CandidateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CandidateController extends AbstractController
{
    private $candidateService;

    public function __construct(CandidateService $candidateService)
    {
        $this->candidateService = $candidateService;
    }

    #[Route('/api/candidates', name: 'get_all_candidates', methods: ['GET'])]
    public function getAllCandidates(): JsonResponse
    {
        $candidates = $this->candidateService->getAllCandidates();
        return $this->json($candidates);
    }

    #[Route('/api/candidates/{id}', name: 'get_candidate', methods: ['GET'])]
    public function getCandidateById($id): JsonResponse
    {
        $candidate = $this->candidateService->getCandidateById($id);
        if (!$candidate) {
            return $this->json(['message' => 'Candidate not found'], 404);
        }
        return $this->json($candidate);
    }
}
```

This revised implementation clearly separates the Write Project (which you provided) from the Read Project. The Read Project is focused solely on querying MongoDB to retrieve candidate information, which aligns with the CQRS pattern.

The Write Project handles the creation and updates of candidates, dispatching messages when changes occur. The Read Project provides an API to query the candidate data stored in MongoDB, which is updated based on the messages dispatched by the Write Project.