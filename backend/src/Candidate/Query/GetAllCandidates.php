<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Query;

use App\Adapter\DataTransformationAdapter;
use App\Document\CandidateDocument;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetAllCandidates extends AbstractQuery implements ItemsQueryInterface
{
    public function __construct
    (
        protected HttpClientInterface $httpClient,
        protected SerializerInterface $serializer,
        protected DataTransformationAdapter $transformationAdapter,
        #[Autowire('%apiBaseUrl%')]
        private readonly string $apiBaseUrl,
    )
    {
        parent::__construct($httpClient, $serializer, $transformationAdapter);
    }

    /**
     * @param array $criteria
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function findItems(array $criteria = []): array
    {
        $url = $this->apiBaseUrl . 'api/candidates';

        $response = $this->makeRequest($url, $criteria);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch candidates: ' . $response->getContent(false));
        }

        return $this->deserializeArray($response->getContent(), CandidateDocument::class);

    }

}