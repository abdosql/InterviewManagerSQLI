<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Handler;
use App\Candidate\Provider\CandidateProvider;
use App\Candidate\Provider\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly abstract class AbstractCandidateHandler implements HandlerInterface
{
    public function __construct
    (
        #[Autowire(service: CandidateProvider::class)]
        protected ProviderInterface $provider)
    {}
}