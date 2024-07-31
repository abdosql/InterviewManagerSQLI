<?php

namespace App\User\Command;
interface CommandInterface
{
    public function execute(): mixed;
    public static function getMessageClass(): string;
}