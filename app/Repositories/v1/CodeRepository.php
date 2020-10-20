<?php

namespace App\Repositories\v1;

use App\Models\Codes;

class CodeRepository implements CodeRepositoryInterface
{
    /**
     * @var Codes
     */
    protected $Codes;

    /**
     * CodeRepository construct
     *
     * @param Codes $codes
     */
    public function __construct(Codes $codes)
    {
        $this->Codes = $codes;
    }

    public function getAll()
    {
        return $this->Codes::All();
    }
    public function find() {}
    public function create() {}
    public function update() {}
    public function delete() {}

    public function getAllData() : object
    {
        return $this->Codes::where('active', 'Y')
            ->orderBy('id', 'asc')
            ->get();
    }
}
