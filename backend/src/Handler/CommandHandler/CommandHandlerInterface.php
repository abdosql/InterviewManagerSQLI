<?php

namespace App\Handler\CommandHandler;

interface CommandHandlerInterface
{
    public function handle(object $command): void;
}