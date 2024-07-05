<?php

namespace App\Entity;

use App\Repository\CVRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CVRepository::class)]
class CV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $file_path = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    public function setFilePath(string $file_path): static
    {
        $this->file_path = $file_path;

        return $this;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(Candidate $candidate): static
    {
        $this->candidate = $candidate;

        return $this;
    }
}
