<?php

namespace App\Repositories\v1;

use App\Model\Codes;

class CodeRepository implements CodeRepositoryInterface
{
    protected $codes;

    public function __construct(Codes $codes)
    {
        $this->codes = $codes;
    }

    public function getAll()
    {
        return $this->codes->all();
    }
    public function find() {}
    public function create() {}
    public function update() {}
    public function delete() {}
}
