<?php

namespace App\Document;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Provider\Data\AppreciationDataProvider;
use App\Repository\AppreciationRepository;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document(collection: "appreciations", repositoryClass: AppreciationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(provider: AppreciationDataProvider::class),
        new Get(provider: AppreciationDataProvider::class),
        new GetCollection(
            uriTemplate: '/appreciations',
            openapiContext: [
                'parameters' => [
                    [
                        'name' => 'interviewId',
                        'in' => 'query',
                        'required' => true,
                        'type' => 'string',
                        'description' => 'Filter appreciations by interview ID'
                    ]
                ]
            ],
            provider: AppreciationDataProvider::class
        ),
    ]
)]
class Appreciation
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $comment;

    #[MongoDB\Field(type: "int")]
    private $score;

    #[MongoDB\ReferenceOne(targetDocument: Interview::class, cascade: ["persist", "remove"], inversedBy: "appreciations")]
    private $interview;

    #[MongoDB\Field(type: "int")]
    protected ?int $entityId;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getInterview(): ?Interview
    {
        return $this->interview;
    }

    public function setInterview(?Interview $interview): self
    {
        $this->interview = $interview;
        return $this;
    }

    public function getEntityId():?int
    {
        return $this->entityId;
    }

    /**
     * @param int|null $entityId
     * @return Appreciation
     */
    public function setEntityId(?int $entityId): self
    {
        $this->entityId = $entityId;
        return $this;
    }
}