<?php

namespace App\Http\Repositories;

use App\Models\MediaFiles;
use Illuminate\Database\Eloquent\Model;

class MediaFilesRepository extends BaseRepository
{
	/**
	 * @var MediaFiles|Model
	 */
	protected Model|MediaFiles $model;

	/**
	 * @param MediaFiles $model
	 */
	public function __construct(MediaFiles $model)
	{
		parent::__construct($model);

		$this->model = $model;
	}

	/**
	 * @param array $payload
	 * @return Model|null
	 */
	public function create(array $payload): ?model
	{
		return parent::create($payload);
	}
}
