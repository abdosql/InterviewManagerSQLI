<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Query;

use App\Document\CandidateDocument;
use App\Entity\Candidate;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetAllCandidate extends AbstractQuery implements ItemsQueryInterface
{
    public function __construct
    (
        protected HttpClientInterface $httpClient,
        protected SerializerInterface $serializer
    )
    {
        parent::__construct($httpClient, $serializer);
    }

    /**
     * @param array|null $criteria
     * @return array
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function findItems(array $criteria = null): array
    {
        if (null === $criteria) {
            $response = $this->httpClient->request(
                'GET',
                'http://localhost:9898/api/candidates'
            );
        }else {
            $response = $this->httpClient->request(
                'GET',
                'http://localhost:9898/api/candidates',
                ['query' => $criteria]
            );
        }
        return $this->serializer->deserialize($response, CandidateDocument::class.'[]', 'json');
    }
}