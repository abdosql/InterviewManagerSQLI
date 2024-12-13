<?php

namespace App\Appreciation\Command\Handler;

use App\Appreciation\Command\CommandInterface;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}