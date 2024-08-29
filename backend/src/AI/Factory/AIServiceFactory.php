<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\AI\Factory;

use App\AI\Service\AiServiceInterface;
use App\AI\Service\TogetherAIService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;

readonly class AIServiceFactory implements AIServiceFactoryInterface
{
    public function __construct(
        #[AutowireLocator('app.ai_service', indexAttribute: 'type')]
        private ServiceProviderInterface $aiServices,
    ) {}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createService(string $serviceType): AiServiceInterface
    {
        if (!$this->aiServices->has($serviceType)) {
            throw new \InvalidArgumentException("Unknown service type: $serviceType");
        }

        return $this->aiServices->get($serviceType);
    }
}