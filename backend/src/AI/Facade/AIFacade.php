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

            $responseContent =  $response['data']['content'];

            $responseContent = trim($responseContent, "{}");
            $responseContent = str_replace('"aiFeedback": ', '', $responseContent);

            $responseContent = str_replace(['\r\n', '\r', '\n'], '', $responseContent);

            if (preg_match("/\'comment\':\s*\'(.*?)\',\s*\'score\':\s*([\d.]+)/", $responseContent, $matches)) {
                $comment = $matches[1];
                $score = floatval($matches[2]);

                return [
                    'comment' => $comment,
                    'score' => $score
                ];
            } else {
                echo "Failed to parse the response.";
            }
        }
        return [];

    }
}