<?php

namespace App\User\Command\Handler;

interface CommandHandlerInterface
{
    public function handle(object $command): void;
}