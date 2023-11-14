<?php

namespace App\Http\Repositories;

use App\Models\Codes;
use Illuminate\Database\Eloquent\Model;

class CodesRepositories extends BaseRepository
{
	/**
	 * @var Model
	 */
	protected Model $modal;

	/**
	 * @param Codes $model
	 */
	public function __construct(Codes $model)
	{
		parent::__construct($model);

		$this->modal = $model;
	}
}
