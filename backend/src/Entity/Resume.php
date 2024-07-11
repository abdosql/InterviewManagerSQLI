<?php

namespace App\Entity;

use App\Repository\ResumeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResumeRepository::class)]
class Resume
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $filePath = null;

    #[ORM\OneToOne(mappedBy: 'resume', cascade: ['persist', 'remove'])]
    private ?Candidate $candidate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $file_path): static
    {
        $this->filePath = $file_path;

        return $this;
    }

    public function getCandidate(): ?Candidate
    {
        return $this->candidate;
    }

    public function setCandidate(?Candidate $candidate): static
    {
        // unset the owning side of the relation if necessary
        if ($candidate === null && $this->candidate !== null) {
            $this->candidate->setResume(null);
        }

        // set the owning side of the relation if necessary
        if ($candidate !== null && $candidate->getResume() !== $this) {
            $candidate->setResume($this);
        }

        $this->candidate = $candidate;

        return $this;
    }
}
