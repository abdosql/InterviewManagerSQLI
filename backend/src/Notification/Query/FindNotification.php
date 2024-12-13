<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Notification\Query;

use App\Adapter\DataTransformationAdapter;
use App\Document\NotificationDocument;
use App\Document\UserDocument;
use App\Entity\Notification;
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
     * @param int $id
     *
     * @return object|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function findItem(int $id): ?Notification
    {
        try {
            $url = $this->apiBaseUrl."api/notifications/{$id}";
            $response = $this->httpClient->request('GET', $url)->getContent();
            $notificationDocument = $this->serializer->deserialize($response, NotificationDocument::class, 'json');
            return $this->transformationAdapter->transformToEntity($notificationDocument, 'notification');
        } catch (HttpExceptionInterface $e) {
            if ($e->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {

                return null;
            }

            throw $e->getResponse();
        }
    }
}