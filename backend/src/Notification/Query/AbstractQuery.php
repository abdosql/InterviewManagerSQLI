<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Notification\Query;

use App\Adapter\DataTransformationAdapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractQuery
{
    public function __construct (
        protected HttpClientInterface $httpClient,
        protected SerializerInterface $serializer,
        protected DataTransformationAdapter $transformationAdapter,

    ) {

    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function makeRequest(string $url, array $criteria = []): ResponseInterface
    {
        $options = array_merge([
            'timeout' => 10,
            'max_redirects' => 0,
            'verify_peer' => false,
            'verify_host' => false,
        ], !empty($criteria) ? ['query' => $criteria] : []);

        return $this->httpClient->request('GET', $url, $options);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function deserializeArray($content, $type): array
    {
        $decodedContent = json_decode($content, true);
        $data = $decodedContent['hydra:member'];

        $documents = [];
        foreach ($data as $document) {
            $deserializedDocument = $this->serializer->deserialize(json_encode($document), $type, 'json');

            $transformedDocument = $this->transformationAdapter->transformToEntity($deserializedDocument, 'notification');

            $documents[] = $transformedDocument;
        }

        return $documents;
    }
}
