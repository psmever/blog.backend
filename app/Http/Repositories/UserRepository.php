<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository
{
	/**
	 * @var Model|User
	 */
	protected Model|User $model;

	/**
	 * @param User $model
	 */
	public function __construct(User $model)
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
