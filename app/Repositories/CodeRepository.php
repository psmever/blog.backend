<?php


namespace App\Repositories;

use App\Models\Codes;

/**
 * Class CodeRepository
 * @package App\Repositories
 */
class CodeRepository implements CodeRepositoryInterface
{
    /**
     * @var Codes
     */
    protected Codes $Codes;

    /**
     * CodeRepository constructor.
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
            ->orderBy('id')
            ->get();
    }
}
