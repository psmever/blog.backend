<?php

return [

	/*
	|--------------------------------------------------------------------------
	| App Data
	|--------------------------------------------------------------------------
	*/

	'basic' => [
		'list_paging' => 15,
		'clientCode' => [
			'front' => '010010',
			'ios' => '010020',
			'android' => '010030',
		],
		'normal_user_level' => '020010',
		'admin_user_level' => '020999',
	],

	'codes' => [
		'client' => [
			'group' => '010',
			'name' => '클라이언트 구분',
			'list' => [
				['code' => '010', 'name' => 'Front'],
				['code' => '020', 'name' => 'iOS'],
				['code' => '030', 'name' => 'Android'],
			]
		],

		'user_level' => [
			'group' => '020',
			'name' => '사용자 레벨',
			'list' => [
				['code' => '000', 'name' => 'guest'],
				['code' => '010', 'name' => '일반 사용자'],
				['code' => '999', 'name' => '관리자'],
			]
		]
	]

];
