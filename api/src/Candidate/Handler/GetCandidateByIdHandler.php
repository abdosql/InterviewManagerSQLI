<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Handler;

use App\Candidate\Provider\CandidateProvider;
use App\Candidate\Provider\ProviderInterface;
use App\Candidate\Query\GetCandidateByEntityId;
use App\Candidate\Query\QueryInterface;
use App\Document\Candidate;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class GetCandidateByIdHandler extends AbstractCandidateHandler
{
    public function __construct(#[Autowire(service: CandidateProvider::class)] ProviderInterface $provider)
    {
        parent::__construct($provider);
    }

    public function handle(QueryInterface $query): Candidate
    {
        if (!$query instanceof GetCandidateByEntityId) {
            throw new \InvalidArgumentException('Invalid query type');
        }
        return $this->provider->getByEntityId($query->getEntityId());
    }
}