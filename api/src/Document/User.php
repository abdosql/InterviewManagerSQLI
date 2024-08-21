<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Provider\Data\UserDataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[ApiResource(
    operations: [
        new GetCollection(provider: UserDataProvider::class),
        new Get(provider: UserDataProvider::class),
        new GetCollection(
            uriTemplate: '/users/by-type',
            openapiContext: [
                'parameters' => [
                    [
                        'name' => 'type',
                        'in' => 'query',
                        'required' => true,
                        'type' => 'string',
                        'description' => 'Filter users by type'
                    ]
                ]
            ],
            provider: UserDataProvider::class
        )
    ]
)]

#[MongoDB\Document(collection: "users")]
#[MongoDB\InheritanceType("SINGLE_COLLECTION")]
#[MongoDB\DiscriminatorField("dType")]
#[MongoDB\DiscriminatorMap(["evaluator" => Evaluator::class, "rh" => HRManager::class, "user" => User::class])]
class User extends Person
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

}