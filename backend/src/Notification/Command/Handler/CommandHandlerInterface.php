<?php

namespace App\Notification\Command\Handler;

interface CommandHandlerInterface
{
    public function handle(object $command): void;
}