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
    public function getAIResponse(string $prompt): string
    {
        $aiService = $this->aiServiceFactory->createService($this->ServiceType);
        $response = $aiService->generateResponse($prompt, $this->DefaultModel);
        return $this->collectResponse($response);
    }

    private function collectResponse(array $response): string
    {
        if ($response['status'] === 'error') {
            throw new \RuntimeException('AI Service error: ' . ($response['message'] ?? 'Unknown error'));
        }

        if ($response['status'] === 'success' && isset($response['data']['content'])) {
            return $response['data']['content'];
        }

        throw new \RuntimeException('Unexpected response format from AI service');
    }
}