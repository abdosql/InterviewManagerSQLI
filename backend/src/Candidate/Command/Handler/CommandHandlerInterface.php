<?php

namespace App\Candidate\Command\Handler;

use App\Candidate\Command\CommandInterface;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}