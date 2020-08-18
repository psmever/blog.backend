<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayGroupCodesList = $this->initGroupCodesList();
	    $arrayCodesList = $this->initCodesList();

	    foreach ($arrayGroupCodesList as $element) :
		    $group_id = trim($element['group_id']);
		    $group_name = trim($element['group_name']);

		    DB::table('codes')->insert([
			    'group_id' => $group_id,
			    'group_name' => $group_name,
			    'created_at' => \Carbon\Carbon::now(),
			    'updated_at' => \Carbon\Carbon::now(),
		    ]);

		    foreach($arrayCodesList[$group_id] as $element_code):

		        $code_id = trim($element_code['code_id']);
		        $code_name = trim($element_code['code_name']);

		        $endCodeid = $group_id.$code_id;

			    DB::table('codes')->insert([
				    'group_id' => $group_id,
				    'group_name' => NULL,
				    'code_id' => $endCodeid,
                    'code_name' => $code_name,
                    'active' => 'Y',
				    'created_at' => \Carbon\Carbon::now(),
				    'updated_at' => \Carbon\Carbon::now(),
			    ]);

			endforeach;
		endforeach;
    }

    /**
	 * 그룹 코드 리스트
	 * @return array
	 */
	public function initGroupCodesList()
    {
	    return [
		    [ 'group_id' => 'S01', 'group_name' => '클라이언트 타입' ],
            [ 'group_id' => 'S02', 'group_name' => '사용자 레벨' ],
            [ 'group_id' => 'S03', 'group_name' => '사용자 상태' ],
            [ 'group_id' => 'S04', 'group_name' => '상태' ],
	    ];
    }


	/**
	 * 코드 리스트
	 * @return array
	 */
	public function initCodesList()
	{
		return [
			'S01' => [
                [ 'code_id' => '010', 'code_name' => 'Front' ],
                [ 'code_id' => '020', 'code_name' => 'iOS' ],
                [ 'code_id' => '030', 'code_name' => 'Android' ],
            ],
            'S02' => [
                [ 'code_id' => '000', 'code_name' => 'Guest' ],
                [ 'code_id' => '010', 'code_name' => '사용자' ],
                [ 'code_id' => '900', 'code_name' => '관리자' ],
                [ 'code_id' => '999', 'code_name' => '최고 관리자' ],
            ],
            'S03' => [
                [ 'code_id' => '000', 'code_name' => '비활성' ],
                [ 'code_id' => '010', 'code_name' => '활성' ],
            ],
            'S04' => [
                [ 'code_id' => '000', 'code_name' => '비사용' ],
                [ 'code_id' => '010', 'code_name' => '사용' ],
            ],
		];
	}
}
