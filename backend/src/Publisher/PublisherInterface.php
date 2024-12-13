<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Publisher;

use App\Entity\User;

interface PublisherInterface
{
    public function publish(array $data, User $user): void;
}