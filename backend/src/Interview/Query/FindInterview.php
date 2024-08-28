<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Interview\Query;

use App\Adapter\DataTransformationAdapter;
use App\Document\InterviewDocument;
use App\Entity\Interview;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FindInterview extends AbstractQuery implements ItemQueryInterface
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
     * @throws TransportExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function findItem(int $id): ?Interview
    {
        try {
            $url = $this->apiBaseUrl."api/interviews/{$id}";
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 10,
                'max_redirects' => 0,
                'verify_peer' => false,
                'verify_host' => false,
            ])->getContent();

            $interviewDocument = $this->serializer->deserialize($response, InterviewDocument::class, 'json');
            return $this->transformationAdapter->transformToEntity($interviewDocument, 'interview');
        } catch (HttpExceptionInterface $e) {
            if ($e->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return null;
            }

            throw $e->getResponse();
        }
    }
}