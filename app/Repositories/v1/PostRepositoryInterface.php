<?php

namespace App\Repositories\v1;

interface PostRepositoryInterface
{
    public function getAll();
    public function find();
    public function create();
    public function update();
    public function delete();
}
