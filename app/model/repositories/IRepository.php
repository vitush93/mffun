<?php

namespace App\Model\Repositories;

interface IRepository
{
    /**
     * @param integer $id
     * @return mixed
     */
    public function find($id);

    /**
     * @return array
     */
    public function findAll();

    /**
     * @param object|array|\Traversable $entity
     */
    public function save($entity);

    /**
     * @param integer $id
     */
    public function remove($id);
} 