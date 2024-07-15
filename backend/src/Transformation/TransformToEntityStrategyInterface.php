<?php

namespace App\Transformation;

interface TransformToEntityStrategyInterface
{
    public function transformToEntity($entity): object;
}