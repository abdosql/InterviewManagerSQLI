<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\AI\Facade;

use App\AI\Factory\AIServiceFactory;
use App\AI\Factory\AIServiceFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class AIFacade
{
    public function __construct(
        #[Autowire(service: AIServiceFactory::class)]
        private AIServiceFactoryInterface $aiServiceFactory,
        #[Autowire(env: "AI_SERVICE_TYPE")]
        private string $ServiceType,
        #[Autowire(env: "AI_DEFAULT_MODEL")]
        private string $DefaultModel
    ) {}

    public function getAIResponse(string $prompt): array
    {
        $aiService = $this->aiServiceFactory->createService($this->ServiceType);
        $response = $aiService->generateResponse($prompt, $this->DefaultModel);

        return $this->collectResponse($response);
    }

    private function collectResponse(array $response): array
    {
        if ($response['status'] === 'error') {
            throw new \RuntimeException('AI Service error: ' . ($response['message'] ?? 'Unknown error'));
        }

        if ($response['status'] === 'success' && isset($response['data']['content'])) {
            $responseContent = $response['data']['content'];

            error_log('Raw AI response: ' . $responseContent);

            $decodedResponse = json_decode($responseContent, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($decodedResponse['comment']) && isset($decodedResponse['score'])) {
                return [
                    'comment' => $decodedResponse['comment'],
                    'score' => floatval($decodedResponse['score'])
                ];
            }

            if (preg_match("/\'comment\':\s*\'(.*?)\',\s*\'score\':\s*([\d.]+)/", $responseContent, $matches)) {
                return [
                    'comment' => $matches[1],
                    'score' => floatval($matches[2])
                ];
            }

            error_log('Failed to parse AI response: ' . $responseContent);
            throw new \RuntimeException('Failed to parse the AI response.');
        }

        throw new \RuntimeException('Unexpected response format from AI service');
    }
}