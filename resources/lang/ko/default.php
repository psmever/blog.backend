<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Default Message
	|--------------------------------------------------------------------------
	*/
    'exception' => [
        'notfound' => '존재 하지 않은 요청 입니다.',
        'notallowedmethod' => '지원되지 않는 메서드입니다.',
        'clienttype' => '클라이언트 정보가 존재 하지 않습니다.',
        'loginFail' => '로그인에 실패 했습니다.',
        'passport_client' => 'Passport 오류가 발생했습니다.',
        'error_exception' => '알수없는 내부 오류가 발생했습니다.',
        'throttle_exception' => '너무 많은 시도 입니다. 잠시후에 다시 시도해 주세요.',
        'pdo_exception' => '데이터 처리중 문제가 발생했습니다.',
        'model_not_found_exception' => '데이터가 존재 하지 않습니다.',
        'forbidden_error_exception' => '권한이 부족합니다.',
    ],

    'server' => [
        'status' => '서버 점검 중입니다.',
        'success' => '정상 전송 하였습니다.',
        'result_success' => '정상 처리 하였습니다.',
        'error' => '오류가 발생 했습니다.',
        'down' => '서버 점검 중입니다.',
    ],

    'login' => [
        'email_required' => '이메일을 입력해 주세요.',
        'email_not_validate' => '이메일 형식을 입력해 주세요.',
        'email_exists' => '존재하지 않는 사용자 입니다.',
        'password_required' => '패스워드를 입력해 주세요.',
        'password_fail' => '비밀번호를 확인해 주세요.',
        'unauthorized' => '로그인이 필요한 서비스 입니다.',
        'refresh_token_not_fount' => '토큰 정보가 없습니다.',
        'refresh_token_fail' => '로그인 정보를 다시 가지고 오는데 오류가 발생했습니다.',
    ],

    'post' => [
        'title_required' => '제목을 입력해 주세요.',
        'category_thumb_required' => '리스트에서 사용할 카테고리 이미지를 선택해주세요.',
        'tags_required' => '테그를 입력해 주세요.',
        'contents_required' => '내용을 입력해 주세요.',
        'auth_error' => '내용을 입력해 주세요.',
    ],
];
