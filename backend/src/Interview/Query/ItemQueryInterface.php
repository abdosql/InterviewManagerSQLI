<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Interview\Query;

use App\Entity\Candidate;

interface ItemQueryInterface
{
    public function findItem(int $id): mixed;
}
