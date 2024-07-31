<?php

namespace App\Candidate\Command\Handler;

interface CommandHandlerInterface
{
    public function handle(object $command): void;
}