<?php

namespace App\Http\Repositories;

use App\Models\Posts;
use Illuminate\Database\Eloquent\Model;

class PostsRepository extends BaseRepository
{
	/**
	 * @var Model|Posts
	 */
	protected Model|Posts $model;

	/**
	 * @param Posts $model
	 */
	public function __construct(Posts $model)
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
