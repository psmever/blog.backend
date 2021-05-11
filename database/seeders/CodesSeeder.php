<?php

namespace Database\Seeders;

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
            [ 'group_id' => 'S05', 'group_name' => '포스트 카테고리 이미지(리스트용)' ],
            [ 'group_id' => 'S06', 'group_name' => '날씨아이콘' ],
            [ 'group_id' => 'S07', 'group_name' => '섹션 포스트 구분' ],
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
            'S05' => [
                [ 'code_id' => '000', 'code_name' => 'blog-default' ],
                [ 'code_id' => '010', 'code_name' => 'front-end' ],
                [ 'code_id' => '020', 'code_name' => 'github' ],
                [ 'code_id' => '030', 'code_name' => 'javascript01' ],
                [ 'code_id' => '040', 'code_name' => 'javascript02' ],
                [ 'code_id' => '050', 'code_name' => 'linux' ],
                [ 'code_id' => '060', 'code_name' => 'mac' ],
                [ 'code_id' => '070', 'code_name' => 'php' ],
                [ 'code_id' => '080', 'code_name' => 'react' ],
                [ 'code_id' => '090', 'code_name' => 'windows' ],
                [ 'code_id' => '990', 'code_name' => 'runners' ],
            ],
            'S06' => [
                [ 'code_id' => '010', 'code_name' => '맑음' ],
                [ 'code_id' => '011', 'code_name' => '맑음 (밤)' ],
                [ 'code_id' => '020', 'code_name' => '구름조금 (낮)' ],
                [ 'code_id' => '021', 'code_name' => '구름조금 (밤)' ],
                [ 'code_id' => '030', 'code_name' => '구름많음 (낮)' ],
                [ 'code_id' => '031', 'code_name' => '구름많음 (밤)' ],
                [ 'code_id' => '040', 'code_name' => '흐림' ],
                [ 'code_id' => '070', 'code_name' => '소나기' ],
                [ 'code_id' => '080', 'code_name' => '비' ],
                [ 'code_id' => '200', 'code_name' => '가끔 비, 한때 비' ],
                [ 'code_id' => '110', 'code_name' => '눈' ],
                [ 'code_id' => '210', 'code_name' => '가끔 눈, 한때 눈' ],
                [ 'code_id' => '120', 'code_name' => '비 또는 눈' ],
                [ 'code_id' => '220', 'code_name' => '가끔 비 또는 눈, 한때 비 또는 눈' ],
                [ 'code_id' => '130', 'code_name' => '눈 또는 비' ],
                [ 'code_id' => '230', 'code_name' => '가끔 눈또는 비, 한때 눈 또는 비' ],
                [ 'code_id' => '140', 'code_name' => '천둥번개' ],
                [ 'code_id' => '180', 'code_name' => '연무' ],
                [ 'code_id' => '150', 'code_name' => '안개' ],
                [ 'code_id' => '170', 'code_name' => '박무 (엷은 안개)' ],
                [ 'code_id' => '160', 'code_name' => '황사' ],
            ],
            'S07' => [
                [ 'code_id' => '010', 'code_name' => '끄적끄적' ],
                [ 'code_id' => '020', 'code_name' => '블러그 소개' ],
                [ 'code_id' => '030', 'code_name' => '주인장은' ],
            ]
        ];
    }
}
