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
    ],

    'server' => [
        'status' => '서버 점검 중입니다.',
        'success' => '정상 전송 하였습니다.',
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
    ],
];
