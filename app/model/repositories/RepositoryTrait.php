<?php

namespace App\Model\Repositories;


trait RepositoryTrait
{
    /**
     * persists given entity
     *
     * @param object $entity
     */
    public function save($entity)
    {
        $this->em->persist($entity);
    }
} 