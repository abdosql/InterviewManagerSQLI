<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Query;

use App\Adapter\DataTransformationAdapter;
use App\Document\CandidateDocument;
use App\Entity\Candidate;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FindCandidate extends AbstractQuery implements ItemQueryInterface
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected SerializerInterface $serializer,
        private readonly DataTransformationAdapter $transformationAdapter,
        #[Autowire('%apiBaseUrl%')]
        private readonly string $apiBaseUrl,
    ) {
        parent::__construct($httpClient, $serializer);
    }

    /**
     * @param int $id
     *
     * @return object|null
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function findItem(int $id): ?Candidate
    {
        try {
            $url = $this->apiBaseUrl."api/candidates/{$id}";
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 10,
                'max_redirects' => 0,
                'verify_peer' => false,
                'verify_host' => false,
            ])->getContent();
            $candidateDocument = $this->serializer->deserialize($response, CandidateDocument::class, 'json');
            return $this->transformationAdapter->transformToEntity($candidateDocument, 'candidate');
        } catch (HttpExceptionInterface $e) {
            if ($e->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {

                return null;
            }

            throw $e->getResponse();
        }
    }
}