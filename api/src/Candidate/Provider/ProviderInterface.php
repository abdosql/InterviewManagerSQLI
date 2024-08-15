<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Provider;

interface ProviderInterface
{
    public function getByEntityId(int $entityId): mixed;
    public function getAll(): array;
    public function getBy(array $criteria): array;
}