<?php

namespace App\Handler\CommandHandler;

interface CommandHandlerInterface
{
    public function execute(object $command): void;
}