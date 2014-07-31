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
     * @return object|array|\Traversable
     */
    public function add($entity);

    /**
     * @param integer $id
     * @return void
     */
    public function remove($id);
} 