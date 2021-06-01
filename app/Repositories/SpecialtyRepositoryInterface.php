<?php

namespace App\Repositories;

interface SpecialtyRepositoryInterface
{
    public function getAll();
    public function find();
    public function create();
    public function update();
    public function delete();
}
