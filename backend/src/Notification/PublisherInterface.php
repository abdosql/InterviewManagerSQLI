<?php
/**
 * @author Saqqal Abdelaziz <seqqal.abdelaziz@gmail.com>
 * @Linkedin https://www.linkedin.com/abdelaziz-saqqal
 */

namespace App\Notification;

use App\Entity\Candidate;
use App\Entity\User;

interface PublisherInterface
{
    public function publish(array $data, User $user): void;
}