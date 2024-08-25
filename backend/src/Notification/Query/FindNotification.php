<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Notification\Query;

use App\Adapter\DataTransformationAdapter;
use App\Document\UserDocument;
use App\Entity\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class FindNotification extends AbstractQuery implements ItemQueryInterface
{
//    public function __construct
//    (
//        protected HttpClientInterface $httpClient,
//        protected SerializerInterface $serializer,
//        protected DataTransformationAdapter $transformationAdapter,
//        #[Autowire('%apiBaseUrl%')]
//        private readonly string $apiBaseUrl,
//    )
//    {
//        parent::__construct($httpClient, $serializer, $transformationAdapter);
//    }

    /**
     * @param int $id
     *
     * @return object|null
     *
     */
    public function findItem(int $id): ?User
    {
//        try {
//            $url = $this->apiBaseUrl."api/users/{$id}";
//            $response = $this->httpClient->request('GET', $url)->getContent();
//            $candidateDocument = $this->serializer->deserialize($response, UserDocument::class, 'json');
//            return $this->transformationAdapter->transformToEntity($candidateDocument, 'user');
//        } catch (HttpExceptionInterface $e) {
//            if ($e->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
//
//                return null;
//            }
//
//            throw $e->getResponse();
//        }
    }
}