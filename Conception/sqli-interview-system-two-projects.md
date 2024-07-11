# Simplified SQLI Interview Management System: CQRS Implementation

## Project Structure

The system is divided into two separate Symfony projects, implementing a CQRS (Command Query Responsibility Segregation) architecture:

1. Write Project (MySQL)
2. Read Project (MongoDB)

### 1. Write Project (MySQL)

```
write-project/
├── src/
│   ├── Command/
│   ├── CommandHandler/
│   ├── Entity/
│   ├── Event/
│   ├── EventDispatcher/
│   ├── Repository/
│   ├── Controller/
│   └── Service/
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
│   ├── Repository/
│   ├── EventHandler/
│   ├── Controller/
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

    #[ORM\Column(type: 'string', length: 255)]
    private $candidateName;

    #[ORM\Column(type: 'string', length: 255)]
    private $evaluatorName;

    #[ORM\Column(type: 'datetime')]
    private $dateTime;

    #[ORM\Column(type: 'string', length: 255)]
    private $location;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    // ... getters and setters
}
```

#### Commands

```php
// write-project/src/Command/ScheduleInterviewCommand.php
namespace App\Command;

class ScheduleInterviewCommand
{
    public function __construct(
        public readonly string $candidateName,
        public readonly string $evaluatorName,
        public readonly \DateTimeImmutable $dateTime,
        public readonly string $location
    ) {}
}
```

#### Command Handlers

```php
// write-project/src/CommandHandler/ScheduleInterviewCommandHandler.php
namespace App\CommandHandler;

use App\Command\ScheduleInterviewCommand;
use App\Entity\Interview;
use App\Event\InterviewScheduledEvent;
use App\EventDispatcher\EventDispatcher;
use Doctrine\ORM\EntityManagerInterface;

class ScheduleInterviewCommandHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcher $eventDispatcher
    ) {}

    public function __invoke(ScheduleInterviewCommand $command): void
    {
        $interview = new Interview();
        $interview->setCandidateName($command->candidateName);
        $interview->setEvaluatorName($command->evaluatorName);
        $interview->setDateTime($command->dateTime);
        $interview->setLocation($command->location);
        $interview->setStatus('Scheduled');

        $this->entityManager->persist($interview);
        $this->entityManager->flush();

        $event = new InterviewScheduledEvent(
            $interview->getId(),
            $interview->getCandidateName(),
            $interview->getEvaluatorName(),
            $interview->getDateTime(),
            $interview->getLocation()
        );

        $this->eventDispatcher->dispatch($event);
    }
}
```

#### Events

```php
// write-project/src/Event/InterviewScheduledEvent.php
namespace App\Event;

class InterviewScheduledEvent
{
    public function __construct(
        public readonly int $interviewId,
        public readonly string $candidateName,
        public readonly string $evaluatorName,
        public readonly \DateTimeImmutable $dateTime,
        public readonly string $location
    ) {}
}
```

#### Event Dispatcher

```php
// write-project/src/EventDispatcher/EventDispatcher.php
namespace App\EventDispatcher;

use Symfony\Component\Messenger\MessageBusInterface;

class EventDispatcher
{
    public function __construct(private MessageBusInterface $eventBus) {}

    public function dispatch($event): void
    {
        $this->eventBus->dispatch($event);
    }
}
```

### 2. Read Project (MongoDB)

#### Documents (Read Models)

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
// read-project/src/EventHandler/InterviewScheduledEventHandler.php
namespace App\EventHandler;

use App\Document\InterviewReadModel;
use App\Event\InterviewScheduledEvent;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

class InterviewScheduledEventHandler
{
    public function __construct(private DocumentManager $documentManager) {}

    #[AsMessageHandler]
    public function __invoke(InterviewScheduledEvent $event): void
    {
        $interviewReadModel = new InterviewReadModel();
        $interviewReadModel->setId($event->interviewId);
        $interviewReadModel->setCandidateName($event->candidateName);
        $interviewReadModel->setEvaluatorName($event->evaluatorName);
        $interviewReadModel->setDateTime($event->dateTime);
        $interviewReadModel->setLocation($event->location);
        $interviewReadModel->setStatus('Scheduled');

        $this->documentManager->persist($interviewReadModel);
        $this->documentManager->flush();
    }
}
```

## Synchronization Between Projects

1. The Write Project dispatches events to RabbitMQ using Symfony Messenger.
2. The Read Project consumes these events and updates the MongoDB database accordingly.

## Security

Implement security in both projects:

1. Write Project: Use Symfony's security component to secure command endpoints.
    - Implement JWT authentication for API endpoints.
    - Use role-based access control (RBAC) to restrict access to certain commands.

2. Read Project: Use API Platform's built-in security features to secure read endpoints.
    - Implement OAuth2 authentication for read API.
    - Use field-level security to restrict access to sensitive information based on user roles.

## Performance Optimizations

1. Write Project:
    - Implement command queueing for better throughput during high load.
    - Use database indexing strategies for faster writes and reads.

2. Read Project:
    - Implement caching mechanisms (e.g., Redis) for frequently accessed read models.
    - Use MongoDB's aggregation pipeline for complex queries.

## Monitoring and Logging

1. Implement centralized logging using ELK stack (Elasticsearch, Logstash, Kibana).
2. Use tools like Prometheus and Grafana for monitoring system metrics.

## Testing

1. Write Project:
    - Implement unit tests for command handlers and services.
    - Use integration tests to ensure proper event dispatching.

2. Read Project:
    - Implement unit tests for event handlers and services.
    - Use integration tests to verify proper updating of read models.

## Deployment

1. Use Docker for containerization of both projects.
2. Implement CI/CD pipelines for automated testing and deployment.

This simplified implementation provides a clear separation between write and read operations, allowing for independent scaling and optimization of each side of the system. It maintains the core benefits of CQRS while reducing the complexity associated with advanced domain modeling techniques.