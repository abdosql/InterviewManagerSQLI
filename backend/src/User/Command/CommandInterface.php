<?php

namespace App\User\Command;
interface CommandInterface
{
    public function execute(): mixed;
}