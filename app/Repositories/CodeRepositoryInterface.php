<?php

namespace App\Repositories;

/**
 * Interface CodeRepositoryInterface
 * @package App\Repositories
 */
interface CodeRepositoryInterface
{
    public function getAll();
    public function find();
    public function create();
    public function update();
    public function delete();
}
