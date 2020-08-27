<?php

namespace App\Repositories\v1;

use App\Model\Codes;

class CodeRepository implements CodeRepositoryInterface
{
    protected $Codes;

    public function __construct(Codes $Codes)
    {
        $this->Codes = $Codes;
    }

    public function getAll()
    {
        return $this->Codes::All();
    }
    public function find() {}
    public function create() {}
    public function update() {}
    public function delete() {}

    public function getAllData()
    {
        return $this->Codes::where('active', 'Y')
            ->orderBy('id', 'asc')
            ->get();
    }
}
