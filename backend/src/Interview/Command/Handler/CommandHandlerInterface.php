<?php

namespace App\Interview\Command\Handler;

interface CommandHandlerInterface
{
    public function handle(object $command): void;
}