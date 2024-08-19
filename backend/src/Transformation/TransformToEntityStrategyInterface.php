<?php

namespace App\Transformation;

interface TransformToEntityStrategyInterface
{
    public function transformToEntity(object $document): mixed;
}