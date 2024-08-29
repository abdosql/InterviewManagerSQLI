<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Appreciation\Query;

interface ItemsQueryInterface
{
    public function findItems(array $criteria = []): array;
}
