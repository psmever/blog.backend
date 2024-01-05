<?php

namespace App\Http\Repositories;

use App\Models\PostTags;
use Illuminate\Database\Eloquent\Model;

class PostTagsRepository extends BaseRepository
{
	/**
	 * @var Model|PostTags
	 */
	protected Model|PostTags $model;

	/**
	 * @param PostTags $model
	 */
	public function __construct(PostTags $model)
	{
		parent::__construct($model);

		$this->model = $model;

	}

	/**
	 * @param array $payload
	 * @return Model|null
	 */
	public function create(array $payload): ?Model
	{
		return parent::create($payload);

	}

}
