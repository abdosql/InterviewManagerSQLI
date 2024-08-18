<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Provider;

interface ProviderInterface
{
    public function getByEntityId(int $entityId): mixed;
    public function getAllOrBy(?array $criteria = null, ?array $orderBy = null, $limit = null, $offset = null): array;
//    public function getBy(array $criteria): array;
}