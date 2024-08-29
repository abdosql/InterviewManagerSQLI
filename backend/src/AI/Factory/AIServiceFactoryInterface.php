<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\AI\Factory;

use App\AI\Service\AiServiceInterface;

interface AIServiceFactoryInterface
{
    public function createService(string $serviceType): AiServiceInterface;
}