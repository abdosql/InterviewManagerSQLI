<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Query;

use App\Entity\Candidate;

interface ItemQueryInterface
{
    public function findItem(int $id): mixed;
}
