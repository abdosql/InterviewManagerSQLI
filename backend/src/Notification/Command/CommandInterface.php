<?php

namespace App\Notification\Command;
interface CommandInterface
{
    public function execute(): mixed;
}