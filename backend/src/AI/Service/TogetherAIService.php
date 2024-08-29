<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\AI\Service;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AutoconfigureTag('app.ai_service', ['type' => 'together'])]
readonly class TogetherAIService implements AiServiceInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: "TOGETHER_API_KEY")]
        private string              $apiKey
    ) {}

    /**
     * @param string $prompt
     * @param string $model
     * @return array
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function generateResponse(string $prompt, string $model = "meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo"): array
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api.together.xyz/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'stream' => false,
                ],
            ]);

            $content = json_decode($response->getContent(), true);

            if (!isset($content['choices'][0]['message']['content'])) {
                throw new \Exception('Unexpected response structure from API');
            }

            return [
                'status' => 'success',
                'data' => [
                    'id' => $content['id'] ?? null,
                    'model' => $content['model'] ?? null,
                    'content' => $content['choices'][0]['message']['content'],
                    'usage' => $content['usage'] ?? null,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}