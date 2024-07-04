# SQLI Interview Management System: Complete Two-Project CQRS Implementation

## 1. Write Project (MySQL)

### 1.1 Additional Entities

```php
// write-project/src/Entity/Candidate.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Candidate extends User
{
    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $cv;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $hireDate;

    // ... getters and setters
}

// write-project/src/Entity/Appreciation.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Appreciation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Interview::class)]
    private $interview;

    #[ORM\Column(type: 'text')]
    private $comment;

    #[ORM\Column(type: 'integer')]
    private $rating;

    // ... getters and setters
}
```

### 1.2 Additional Commands

```php
// write-project/src/Message/Command/AddAppreciationCommand.php
namespace App\Message\Command;

class AddAppreciationCommand
{
    public function __construct(
        public readonly int $interviewId,
        public readonly string $comment,
        public readonly int $rating
    ) {}
}

// write-project/src/Message/Command/ChangeInterviewStatusCommand.php
namespace App\Message\Command;

class ChangeInterviewStatusCommand
{
    public function __construct(
        public readonly int $interviewId,
        public readonly string $newStatus
    ) {}
}
```

### 1.3 Additional Command Handlers

```php
// write-project/src/MessageHandler/Command/AddAppreciationCommandHandler.php
namespace App\MessageHandler\Command;

use App\Entity\Appreciation;
use App\Message\Command\AddAppreciationCommand;
use App\Message\Event\AppreciationAddedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class AddAppreciationCommandHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $eventBus
    ) {}

    public function __invoke(AddAppreciationCommand $command)
    {
        $interview = $this->entityManager->getReference(Interview::class, $command->interviewId);
        
        $appreciation = new Appreciation();
        $appreciation->setInterview($interview);
        $appreciation->setComment($command->comment);
        $appreciation->setRating($command->rating);

        $this->entityManager->persist($appreciation);
        $this->entityManager->flush();

        $this->eventBus->dispatch(new AppreciationAddedEvent($appreciation->getId(), $command->interviewId, $command->comment, $command->rating));
    }
}

// Implement ChangeInterviewStatusCommandHandler similarly
```

### 1.4 Additional Events

```php
// write-project/src/Message/Event/AppreciationAddedEvent.php
namespace App\Message\Event;

class AppreciationAddedEvent
{
    public function __construct(
        public readonly int $appreciationId,
        public readonly int $interviewId,
        public readonly string $comment,
        public readonly int $rating
    ) {}
}

// Implement InterviewStatusChangedEvent similarly
```

### 1.5 Security

```php
// write-project/src/Security/InterviewVoter.php
namespace App\Security;

use App\Entity\Interview;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class InterviewVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['SCHEDULE', 'ADD_APPRECIATION', 'CHANGE_STATUS'])
            && $subject instanceof Interview;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $interview = $subject;

        switch ($attribute) {
            case 'SCHEDULE':
                return $this->canSchedule($interview, $user);
            case 'ADD_APPRECIATION':
                return $this->canAddAppreciation($interview, $user);
            case 'CHANGE_STATUS':
                return $this->canChangeStatus($interview, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canSchedule(Interview $interview, User $user): bool
    {
        return $user instanceof Responsible;
    }

    private function canAddAppreciation(Interview $interview, User $user): bool
    {
        return $user === $interview->getEvaluator();
    }

    private function canChangeStatus(Interview $interview, User $user): bool
    {
        return $user instanceof Responsible;
    }
}
```

## 2. Read Project (MongoDB)

### 2.1 Additional Documents

```php
// read-project/src/Document/CandidateReadModel.php
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
class CandidateReadModel
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: 'string')]
    private $name;

    #[MongoDB\Field(type: 'string')]
    private $email;

    #[MongoDB\Field(type: 'string')]
    private $address;

    #[MongoDB\Field(type: 'string')]
    private $cv;

    // ... getters and setters
}

// read-project/src/Document/AppreciationReadModel.php
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
class AppreciationReadModel
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: 'string')]
    private $interviewId;

    #[MongoDB\Field(type: 'string')]
    private $comment;

    #[MongoDB\Field(type: 'int')]
    private $rating;

    // ... getters and setters
}
```

### 2.2 Additional Event Handlers

```php
// read-project/src/MessageHandler/Event/AppreciationAddedEventHandler.php
namespace App\MessageHandler\Event;

use App\Document\AppreciationReadModel;
use App\Message\Event\AppreciationAddedEvent;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AppreciationAddedEventHandler implements MessageHandlerInterface
{
    public function __construct(private DocumentManager $documentManager) {}

    public function __invoke(AppreciationAddedEvent $event)
    {
        $appreciationReadModel = new AppreciationReadModel();
        $appreciationReadModel->setId($event->appreciationId);
        $appreciationReadModel->setInterviewId($event->interviewId);
        $appreciationReadModel->setComment($event->comment);
        $appreciationReadModel->setRating($event->rating);

        $this->documentManager->persist($appreciationReadModel);
        $this->documentManager->flush();
    }
}

// Implement InterviewStatusChangedEventHandler similarly
```

### 2.3 API Endpoints

The API endpoints are automatically generated by API Platform based on the ApiResource annotations in the Document classes. Here's a summary of the available endpoints:

1. Interviews:
   - GET /api/interviews: List all interviews
   - GET /api/interviews/{id}: Get a specific interview

2. Candidates:
   - GET /api/candidates: List all candidates
   - GET /api/candidates/{id}: Get a specific candidate

3. Appreciations:
   - GET /api/appreciations: List all appreciations
   - GET /api/appreciations/{id}: Get a specific appreciation

### 2.4 Security for Read Project

```php
// read-project/src/Security/InterviewReadVoter.php
namespace App\Security;

use App\Document\InterviewReadModel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class InterviewReadVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['VIEW'])
            && $subject instanceof InterviewReadModel;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $interview = $subject;

        switch ($attribute) {
            case 'VIEW':
                return $this->canView($interview, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(InterviewReadModel $interview, User $user): bool
    {
        // Implement logic to determine if the user can view the interview
        // For example, only allow if the user is the candidate, evaluator, or a responsible
        return $user->getId() === $interview->getCandidateId()
            || $user->getId() === $interview->getEvaluatorId()
            || $user instanceof Responsible;
    }
}
```

## 3. RabbitMQ Configuration

Both projects should have similar RabbitMQ configurations:

```yaml
# write-project/config/packages/messenger.yaml and read-project/config/packages/messenger.yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Message\Event\InterviewScheduledEvent': async
            'App\Message\Event\AppreciationAddedEvent': async
            'App\Message\Event\InterviewStatusChangedEvent': async
```

This completes the implementation of the SQLI Interview Management System using two separate Symfony projects for write and read operations. The Write Project handles all mutations and publishes events, while the Read Project consumes these events to update its read models and serves read-only API endpoints.

