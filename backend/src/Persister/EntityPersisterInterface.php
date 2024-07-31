<?php

namespace App\Persister;

interface EntityPersisterInterface
{
    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    public function save(object $entity, bool $flush = true): void;

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    public function update(object $entity, bool $flush = true): void;

    /**
     * @param object $entity
     * @param bool $flush
     * @return void
     */
    public function delete(object $entity, bool $flush = true): void;
}