<?php

namespace App\Http\Services;

use App\Exceptions\ClientErrorException;
use App\Http\Repositories\UserRepositories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Str;
use Validator;

class UserService
{

	protected Request $request;

	protected UserRepositories $userRepositories;

	function __construct(Request $request, UserRepositories $userRepositories)
	{
		$this->request = $request;
		$this->userRepositories = $userRepositories;
	}


	/**
	 * @throws ClientErrorException
	 */
	public function testUserCreate(): array
	{
		$uuid = Str::random(10);

		$validator = Validator::make($this->request->all(), [
			'type' => 'required',
			'level' => 'required',
			'name' => 'required',
			'email' => 'required|unique:users',
			'password' => 'required',
		],
			[
				'type.required' => '타입을 입력해 주세요',
				'level.required' => '레벨을 입력해 주세요.',
				'name.required' => '이름을 입력해 주세요.',
				'email.required' => '이메일을 입력해 주세요.',
				'email.unique' => '존재하는 이메일 입니다.',
				'password.required' => '비밀번호를 입력해 주세요.',
			]);

		//$validator->passes()
		if ($validator->fails()) {
			throw new ClientErrorException($validator->errors()->first());
		}

		return $this->userRepositories->create([
			'uuid' => $uuid,
			'type' => $this->request->input('type'),
			'level' => $this->request->input('level'),
			'name' => $this->request->input('name'),
			'email' => $this->request->input('email'),
			'password' => $this->request->input('password'),
			'email_verified_at' => Carbon::now(),
		])->toArray();
	}
}
