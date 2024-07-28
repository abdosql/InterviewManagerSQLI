<?php

namespace App\Command;

interface CommandHandlerInterface
{
    public function handle(object $command): void;
}