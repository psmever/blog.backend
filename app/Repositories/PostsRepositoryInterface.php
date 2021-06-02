<?php

namespace App\Repositories;

/**
 * Interface PostsRepositoryInterface
 * @package App\Repositories
 */
interface PostsRepositoryInterface
{
    public function getAll();
    public function find();
    public function create();
    public function update();
    public function delete();
}
