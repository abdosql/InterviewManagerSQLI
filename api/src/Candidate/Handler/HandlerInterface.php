<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Candidate\Handler;

use App\Candidate\Query\QueryInterface;

interface HandlerInterface
{
    public function handle(QueryInterface $query): mixed;
}