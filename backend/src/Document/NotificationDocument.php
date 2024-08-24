<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "notifications")]
class NotificationDocument
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $content;

    #[MongoDB\Field(type: "date")]
    private $notificationDate;

    #[MongoDB\ReferenceOne(targetDocument: UserDocument::class, inversedBy: "notifications")]
    private $user;

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

    public function getUser(): ?UserDocument
    {
        return $this->user;
    }

    public function setUser(?UserDocument $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function getEntityId():?int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     * @return NotificationDocument
     */
    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }
}