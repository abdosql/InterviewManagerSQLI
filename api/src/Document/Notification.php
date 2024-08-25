<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Provider\Data\NotificationDataProvider;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[ApiResource(
    operations: [
        new GetCollection(provider: NotificationDataProvider::class),
        new Get(provider: NotificationDataProvider::class),
        new GetCollection(
            uriTemplate: '/notifications',
            openapiContext: [
                'parameters' => [
                    [
                        'name' => 'userId',
                        'in' => 'query',
                        'required' => true,
                        'type' => 'string',
                        'description' => 'Filter notifications by user ID'
                    ]
                ]
            ],
            provider: NotificationDataProvider::class
        ),
    ]
)]
#[MongoDB\Document(collection: "notifications")]
class Notification
{
    #[MongoDB\Id]
    private ?string $id;

    #[MongoDB\Field(type: "string")]
    private ?string $content;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $notificationDate;

    #[MongoDB\ReferenceOne(targetDocument: User::class, inversedBy: "notifications")]
    private ?User $user;

    #[MongoDB\Field(type: "boolean")]
    private ?bool $is_read = false;

    #[MongoDB\Field(type: "string")]
    private ?string $link = null;

    #[MongoDB\Field(type: "int")]
    protected ?int $entityId;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getNotificationDate(): ?\DateTimeInterface
    {
        return $this->notificationDate;
    }

    public function setNotificationDate(\DateTimeInterface $notificationDate): self
    {
        $this->notificationDate = $notificationDate;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function isRead(): ?bool
    {
        return $this->is_read;
    }

    public function setRead(bool $is_read): static
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }
    public function getEntityId():?int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     * @return Notification
     */
    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }
}