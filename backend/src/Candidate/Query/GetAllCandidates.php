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
        private readonly DataTransformationAdapter $transformationAdapter,
        #[Autowire('%apiBaseUrl%')]
        private readonly string $apiBaseUrl,
    )
    {
        parent::__construct($httpClient, $serializer);
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

        $response = $this->httpClient->request(
            'GET',
            $url,
            array_merge([
                'timeout' => 10,
                'max_redirects' => 0,
                'verify_peer' => false,
                'verify_host' => false,
            ], !empty($criteria) ? ['query' => $criteria]    : [])
        );

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to fetch candidates: ' . $response->getContent(false));
        }

        $decodedContent = json_decode($response->getContent(), true);
        $candidateData = $decodedContent['hydra:member'];

        $candidateDocuments = [];
        foreach ($candidateData as $candidateItem) {
            $candidateDocument = $this->serializer->deserialize(json_encode($candidateItem), CandidateDocument::class, 'json');
            $candidateDocuments[] = $this->transformationAdapter->transformToEntity($candidateDocument, 'candidate');
        }
        return $candidateDocuments;
    }

}