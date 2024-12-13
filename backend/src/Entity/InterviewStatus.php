<?php

namespace App\Entity;

use App\Repository\EntityRepository\InterviewStatusRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterviewStatusRepository::class)]
class InterviewStatus
{
    public const SCHEDULED = 'SCHEDULED';
    public const IS_FAILED = 'IS_FAILED';
    public const IS_PASSED = 'IS_PASSED';
    public const IN_PROGRESS = 'IN_PROGRESS';


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $status_date = null;

    #[ORM\ManyToOne(inversedBy: 'interviewStatuses')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Interview $interview = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatusDate(): ?\DateTimeInterface
    {
        return $this->status_date;
    }

    public function setStatusDate(\DateTimeInterface $status_date): static
    {
        $this->status_date = $status_date;

        return $this;
    }

    public function getInterview(): ?Interview
    {
        return $this->interview;
    }

    public function setInterview(?Interview $interview): static
    {
        $this->interview = $interview;

        return $this;
    }
    

}
