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

    #[MongoDB\ReferenceOne(storeAs: "dbRef", targetDocument: UserDocument::class, inversedBy: "notifications")]
    private $user;

    #[MongoDB\Field(type: "boolean")]
    private ?bool $is_read = false;

    #[MongoDB\Field(type: "string")]
    private ?string $link = null;

    #[MongoDB\Field(type: "date")]
    private ?\DateTimeInterface $createdAt = null;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setDocument(NotificationDocument $notificationDocument): void
    {
        $this->content = $notificationDocument->getContent();
        $this->notificationDate = $notificationDocument->getNotificationDate();
        $this->user = $notificationDocument->getUser();
        $this->is_read = $notificationDocument->isRead();
        $this->link = $notificationDocument->getLink();
        $this->createdAt = $notificationDocument->getCreatedAt();
        $this->entityId = $notificationDocument->getEntityId();
    }

}