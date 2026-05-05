# Postman Collection Export

- `php artisan postman:export`로 현재 Laravel API 라우트를 Postman 컬렉션 JSON으로 생성합니다.
- 기본 출력 파일은 `storage/app/postman/blog-backend.postman_collection.json`입니다.
- 컬렉션 변수는 포함하지 않습니다.
- 활성 환경(`local`)에는 `blog_api_base_url`, `user_email`, `user_password`, `user_id`를 둡니다.
- Globals에는 `postman_client_type`, `access_token`, `refresh_token`, `access_token_expires_at`, `refresh_token_expires_at`를 둡니다.
- 다른 경로가 필요하면 `php artisan postman:export --output=/custom/path.json`을 사용합니다.
- 자주 쓰면 `composer postman:export`로 같은 작업을 실행할 수 있습니다.
