<?php

namespace App\Candidate\Command;
interface CommandInterface
{
    public function execute(): mixed;
    public static function getMessageClass(): string;
}