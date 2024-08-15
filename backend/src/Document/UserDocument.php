<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "users")]
#[MongoDB\InheritanceType("SINGLE_COLLECTION")]
#[MongoDB\DiscriminatorField("dType")]
#[MongoDB\DiscriminatorMap(["evaluator" => EvaluatorDocument::class, "rh" => HRManagerDocument::class, "user" => UserDocument::class])]
class UserDocument extends PersonDocument
{
    #[MongoDB\Id]
    protected ?string $id;

    #[MongoDB\Field(type: "string")]
    protected ?string $username;

    #[MongoDB\Field(type: "string")]
    protected ?string $password;

    #[MongoDB\Field(type: "collection")]
    protected array $roles = [];

    #[MongoDB\ReferenceMany(targetDocument: Notification::class, mappedBy: "user")]
    protected ArrayCollection $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }
        return $this;
    }

    public function setDocument(UserDocument $document): void
    {
        $this->firstName = $document->getFirstName();
        $this->lastName = $document->getLastName();
        $this->phone = $document->getPhone();
        $this->email = $document->getEmail();
        $this->entityId = $document->getEntityId();
        $this->username = $document->getUsername();
        $this->password = $document->getPassword();
        $this->roles = $document->getRoles();
    }
}