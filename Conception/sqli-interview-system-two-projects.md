# SQLI Interview Management System: Two-Project CQRS Implementation

## Project Structure

You now have two separate Symfony projects:

1. Write Project (MySQL)
2. Read Project (MongoDB)

### 1. Write Project (MySQL)

```
write-project/
├── src/
│   ├── Command/
│   ├── Entity/
│   ├── MessageHandler/
│   ├── Controller/
│   ├── Repository/
│   ├── Service/
│   └── Message/
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
│   ├── Controller/
│   ├── Repository/
│   └── Service/
├── config/
│   ├── packages/
│   │   ├── doctrine_mongodb.yaml
│   │   └── api_platform.yaml
│   └── services.yaml
└── .env
```

## Implementation Details

### 1. Write Project (MySQL)

#### Entities

```php
// write-project/src/Entity/Interview.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Interview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Candidate::class)]
    private $candidate;

    #[ORM\ManyToOne(targetEntity: Evaluator::class)]
    private $evaluator;

    #[ORM\Column(type: 'datetime')]
    private $dateTime;

    #[ORM\Column(type: 'string', length: 255)]
    private $location;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    // ... getters and setters
}

// Similarly, create other entities like User, Candidate, Evaluator, etc.
```

#### Commands

```php
// write-project/src/Message/Command/ScheduleInterviewCommand.php
namespace App\Message\Command;

class ScheduleInterviewCommand
{
    public function __construct(
        public readonly int $candidateId,
        public readonly int $evaluatorId,
        public readonly \DateTimeImmutable $dateTime,
        public readonly string $location
    ) {}
}
```

#### Command Handlers

```php
// write-project/src/MessageHandler/Command/ScheduleInterviewCommandHandler.php
namespace App\MessageHandler\Command;

use App\Entity\Interview;
use App\Message\Command\ScheduleInterviewCommand;
use App\Message\Event\InterviewScheduledEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class ScheduleInterviewCommandHandler
{
    use HandleTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $eventBus
    ) {}

    public function __invoke(ScheduleInterviewCommand $command)
    {
        $interview = new Interview();
        $interview->setCandidate($this->entityManager->getReference(Candidate::class, $command->candidateId));
        $interview->setEvaluator($this->entityManager->getReference(Evaluator::class, $command->evaluatorId));
        $interview->setDateTime($command->dateTime);
        $interview->setLocation($command->location);
        $interview->setStatus('scheduled');

        $this->entityManager->persist($interview);
        $this->entityManager->flush();

        // Dispatch an event to be picked up by the read model
        $this->eventBus->dispatch(new InterviewScheduledEvent($interview->getId(), $interview->getCandidate()->getId(), $interview->getEvaluator()->getId(), $interview->getDateTime()->format('Y-m-d H:i:s'), $interview->getLocation(), $interview->getStatus()));
    }
}
```

#### Messenger Configuration

```yaml
# write-project/config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Message\Event\InterviewScheduledEvent': async
```

### 2. Read Project (MongoDB)

#### Documents

```php
// read-project/src/Document/InterviewReadModel.php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[MongoDB\Document]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()
    ]
)]
class InterviewReadModel
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: 'string')]
    private $candidateName;

    #[MongoDB\Field(type: 'string')]
    private $evaluatorName;

    #[MongoDB\Field(type: 'date')]
    private $dateTime;

    #[MongoDB\Field(type: 'string')]
    private $location;

    #[MongoDB\Field(type: 'string')]
    private $status;

    // ... getters and setters
}
```

#### Event Handlers

```php
// read-project/src/MessageHandler/Event/InterviewScheduledEventHandler.php
namespace App\MessageHandler\Event;

use App\Document\InterviewReadModel;
use App\Message\Event\InterviewScheduledEvent;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class InterviewScheduledEventHandler implements MessageHandlerInterface
{
    public function __construct(private DocumentManager $documentManager) {}

    public function __invoke(InterviewScheduledEvent $event)
    {
        $interviewReadModel = new InterviewReadModel();
        $interviewReadModel->setId($event->interviewId);
        $interviewReadModel->setCandidateName($event->candidateName);
        $interviewReadModel->setEvaluatorName($event->evaluatorName);
        $interviewReadModel->setDateTime(new \DateTime($event->dateTime));
        $interviewReadModel->setLocation($event->location);
        $interviewReadModel->setStatus($event->status);

        $this->documentManager->persist($interviewReadModel);
        $this->documentManager->flush();
    }
}
```

#### API Platform Configuration

```yaml
# read-project/config/packages/api_platform.yaml
api_platform:
    mapping:
        paths: ['%kernel.project_dir%/src/Document']
    patch_formats:
        json: ['application/merge-patch+json']
    swagger:
        versions: [3]
```

## Synchronization Between Projects

1. The Write Project dispatches events (e.g., `InterviewScheduledEvent`) to RabbitMQ.
2. The Read Project consumes these events and updates the MongoDB database accordingly.

## Security

Implement security in both projects:

1. Write Project: Use Symfony's security component to secure command endpoints.
2. Read Project: Use API Platform's built-in security features to secure read endpoints.

