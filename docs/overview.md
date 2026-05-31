# Blog Backend Overview

## Purpose & Scope

- Laravel 12 기반 블로그 백엔드 서비스로, Next.js 프런트엔드(`blog.frontend`)와 연동되는 API 계층을 제공합니다.
- Docker Compose 프로젝트(`blog.workspace`)에서 PHP-FPM, MariaDB, Node 컨테이너와 함께
  구동되며, 로컬 개발과 배포 전 테스트를 목표로 합니다.
- `docs/TASKS.md`(개발 진행표)와 연계하여 현재 작업 상황과 우선순위를 공유합니다.

## 상위 프로젝트 구조

```text
blog/
 ├── blog.backend/       # Laravel 백엔드 (현재 위치)
 ├── blog.frontend/      # Next.js 프런트엔드
 └── blog.workspace/     # Docker 설정 및 실행 스크립트
```

- 본 문서는 `blog.backend` 디렉터리를 기준으로 작성되었습니다.
- 공용 스크립트 및 컨테이너 관리는 `blog.workspace` 디렉터리에서 이뤄집니다.

## 기술 스택

- `PHP 8.5` + `Laravel 12.x`
- MariaDB (Docker 컨테이너)
- Composer / NPM
- 코드 품질: Laravel Pint, PHPStan + Larastan, PHPUnit

## 애플리케이션 아키텍처

1. **엔트리 포인트**: HTTP 요청은 `routes/`에서 버전별 라우트 파일
   (`routes/api/api.php`, `routes/api/v1.php`)로 분기됩니다.
2. **컨트롤러 레이어**: `app/Http/Controllers/Api`에 `ApiBaseController`,
   `HealthController` 등이 위치하며 공통 응답 처리와 예외 처리를 위임받습니다.
3. **응답/예외 처리**:
   - `app/Traits/ApiResponseTrait.php`: 일관된 `success`, `message`, `data` 포맷 제공.
   - `app/Exceptions/ApiException.php`: API 전용 예외 처리 로직.
4. **추가 예정 계층** (개발 진행표 참조):
   - 인증 (Sanctum/JWT), Repository/Service 계층, Post 도메인 모델.

## 디렉터리 가이드

- `app/` - 핵심 Laravel 애플리케이션 로직.
  - `Http/Controllers/Api` - API 전용 컨트롤러.
  - `Traits/` - API / Web 응답 트레이트.
  - `Exceptions/` - API 예외 처리.
- `routes/api/` - API 엔드포인트 정의(`api.php`, 버전별 `v1.php`).
- `database/` - 마이그레이션, 팩토리, 시더.
- `config/` - 환경별 설정.
- `scripts/` - Docker/Artisan 헬퍼 스크립트(`artisan.sh` 등).
- `docs/` - 프로젝트 문서 (`TASKS.md`, `overview.md`).

README의 [프로젝트 구조](../README.md) 섹션에서 전체 파일 트리를 확인할 수 있습니다.

## 개발 환경 & 실행

### 컨테이너 관리

- `cd ../blog.workspace && make up local` : 로컬 Docker 서비스 기동
- `make down`, `make logs` 등 보조 명령도 동일 디렉터리에서 실행합니다.

### Artisan 명령 실행

- 기본 형태: `php artisan <command>` (예: `php artisan migrate`)
- Docker 스크립트 이용:
  - `blog.workspace/scripts/artisan.sh <command>`
  - `blog.backend` 기준 상대 경로 예시: `../blog.workspace/scripts/artisan.sh migrate`
- 스크립트는 컨테이너 안에서 실행되므로 PHP/DB 연결을 별도 설정할 필요가 없습니다.

### 개발 편의 스크립트

- `composer dev` (`composer run dev`) 스크립트가 PHP 서버, 큐, 로그 모니터링, Vite 개발 서버를 동시에 실행합니다.

## 품질 관리 도구

- `./vendor/bin/pint` : PSR-12 기반 코드 스타일.
- `./vendor/bin/phpstan analyse` : 정적 분석 (기본 레벨 5, 캐시 `storage/phpstan`).
- `php artisan test` : PHPUnit 테스트 러너 (`composer test` 스크립트로도 실행 가능).

## 현재 API 표면

- `GET /api/health` : 헬스 체크
- `GET /api/v1/demo` : 샘플 테스트 엔드포인트 (향후 제거/대체 예정)
- `GET /api/v1/public/posts?limit=12&cursor=...` : 공개 발행글 목록 조회 (인증 불필요)
- `GET /api/v1/public/posts/{slug}` : 공개 발행글 상세 조회 + 세션 기준 조회수 증가 (인증 불필요)
- `POST /api/v1/posts/uuid` : 게시글 UUID 사전 발급 (인증 필요, DB 저장 없음)
- `POST /api/v1/posts` : 새 글 등록 (인증 필요)
- `GET /api/v1/posts?status=published|draft&limit=20` : 상태별 내 글 목록 조회 (인증 필요)
- `GET /api/v1/posts/{uuid}` : UUID로 게시글 상세 조회 (인증 필요)
- `POST /api/v1/posts/{uuid}/images` : 게시글 본문 이미지 업로드 (인증 필요)
- `POST /api/v1/posts/{uuid}/save` : UUID로 임시 저장 상태 전환 (인증 필요)
- `POST /api/v1/posts/{uuid}/publish` : UUID로 게시글 개시 (인증 필요)

## 새 글 등록 API

- 인증 헤더: `Authorization: Bearer <access_token>`
- 요청 바디 예시:

```json
{
  "title": "Hello React",
  "tags": ["react", "Next.js"],
  "body": "본문 내용"
}
```

- 응답 예시:

```json
{
  "message": "정상 처리되었습니다",
  "data": {
    "uuid": "b6b1c8c6-6b6f-4b0c-9f7c-4c7f51b8d0b1"
  },
  "meta": {
    "status": 201,
    "timestamp": "2026-02-08 10:00:00"
  }
}
```

- 태그 정규화: 입력값을 기반으로 `key`는 소문자 slug, `label`은 보기용 표기(예: `react` → `{ key: react, label: React }`).
- slug 생성: 동일 사용자 기준 중복 시 `-2`, `-3` 접미사로 처리합니다.
- 게시글 상태 코드는 `common_codes`의 `group_key=post.status` 활성 코드와 연결되어 검증됩니다.
- 상태 변경 이력은 `post_status_histories` 테이블에 기록됩니다.

## 이미지 업로드 API

- 로컬 개발은 `MEDIA_DISK=public`, 운영 배포는 `MEDIA_DISK=s3`를 사용합니다.
- 로컬과 운영 모두 업로드 경로 prefix는 `MEDIA_ROOT`를 사용하며 기본값은 `blog`입니다.
- 로컬 공개 URL 사용을 위해 `php artisan storage:link`가 필요합니다.
- 업로드 요청은 `multipart/form-data` 형식입니다.
- 글 등록 화면에서는 먼저 `POST /api/v1/posts/uuid`로 UUID만 발급받고, 저장 전에도 해당 UUID로 이미지를 업로드할 수 있습니다.
- 저장 전 업로드된 이미지는 `POST /api/v1/posts/{uuid}/save` 호출 시 생성되는 게시글에 자동 연결됩니다.

```http
POST /api/v1/posts/{uuid}/images
```

요청 필드:

- `image`: jpeg, jpg, png, webp, gif / 최대 10MB
- `purpose`: 선택 전달값이며 현재는 무시됩니다. 업로드 이미지는 모두 본문 이미지(`body`)로 저장됩니다.
- 게시글 대표 이미지는 별도 API로 지정하지 않고, `POST /api/v1/posts/{uuid}/save` 또는 생성 시점에 본문 Markdown의 첫 번째 이미지 기준으로 자동 동기화됩니다.
- 업로드 시 홈 카드용 `800x550` WebP 썸네일을 `posts/{postUuid}/thumbnail/{imageUuid}.webp` 경로에 함께 생성합니다.
- 썸네일 생성에는 PHP GD 확장의 JPEG, PNG, GIF, WebP 지원이 필요하며, 변환 또는 저장 실패 시 원본 업로드도 실패 처리됩니다.
- 이미지 응답의 `thumbnail` 객체는 썸네일 URL, 너비, 높이, 용량, MIME 타입을 포함합니다. 기존 이미지가 아직 변환되지 않았거나 기본 커버 이미지이면 `thumbnail`은 `null`입니다.
- 기존 업로드 이미지의 누락된 썸네일은 `php artisan posts:backfill-thumbnails` 명령으로 생성합니다.

## 공개 블로그 API

- 공개 목록은 `GET /api/v1/public/posts`로 조회하며 `status=published` 글만 최신 공개일 순(`published_at DESC, id DESC`)으로 반환합니다.
- 기본 `limit`은 `12`, 최대 `50`이며 다음 페이지는 `meta.next_cursor`를 그대로 `cursor` 쿼리에 전달해 조회합니다.
- 공개 목록 응답에는 `slug`, `title`, `excerpt`, `published_at`, `cover_image`, `author.name`, `primary_tag`, `view_count`가 포함됩니다.
- 공개 목록/상세의 `cover_image`는 대표 이미지가 없을 때 `POST_DEFAULT_COVER_IMAGE_URL`로 지정한 기본 이미지를 반환하며, `is_default` 값으로 업로드 이미지 여부를 구분할 수 있습니다. 운영 환경에서는 CDN 이미지(`https://cdn.jaubi.co.kr/blog/assets/default-cover.png`)를 사용합니다.
- 공개 상세는 `GET /api/v1/public/posts/{slug}`로 조회하며 markdown 원문 `body`와 함께 `view_count`를 반환합니다.
- 공개 상세 조회수는 Laravel 세션 쿠키 기준으로 동일 세션에서 같은 글을 반복 조회해도 한 번만 증가합니다.

## 문서 & 참고

- 개발 진행 상황: `docs/TASKS.md`
- 환경 변수 정의: `.env.example` (실제 `.env`는 각 환경에서 수동 관리하며 Git에 커밋하지 않음)
- 향후 작성 예정: 배포 전략, 인증/서비스 계층 설계 문서

필요 시 본 문서에 추가 섹션(예: ERD, 시퀀스 다이어그램, CI 파이프라인)을 만들어 협업 중 발견한 내용을 계속 보완해 주세요.
