# Gemini Project Analysis: blog.backend

## 🌟 프로젝트 개요

이 프로젝트는 **Laravel 12** 프레임워크를 사용하여 구축된 블로그 애플리케이션의 백엔드 API입니다. 프런트엔드 클라이언트에 데이터를 제공하고, 콘텐츠 관리(게시물, 태그) 및 사용자 인증을 처리합니다.

---

## 🛠️ 기술 스택

### 백엔드
- **PHP 버전**: `^8.2`
- **프레임워크**: Laravel `^12.0`
- **웹 서버**: **Laravel Octane** (`^2.13`)에 최적화되어 있습니다.
- **인증**: API 토큰 기반 인증을 위해 **Laravel Sanctum** (`^4.2`)을 사용합니다.
- **데이터베이스**: 관계형 데이터베이스(아마도 MySQL/MariaDB)를 위한 마이그레이션이 설정되어 있습니다. 테스트는 인메모리 **SQLite** 데이터베이스를 사용하도록 구성되어 있습니다.

### 프런트엔드 & 에셋 번들링
- **Node.js 빌드 도구**: **Vite** (`^7.0.4`)
- **CSS 프레임워크**: **Tailwind CSS** (`^4.0.0`)
- **HTTP 클라이언트**: JS에서 요청을 보내기 위해 **Axios** (`^1.11.0`)를 포함합니다.

---

## 🏗️ 아키텍처 통찰

### 디렉토리 구조 및 주요 파일
- `app/Http/Controllers/Api/V1/`: `PostController` 및 `SystemController`와 같은 버전 관리되는 API 컨트롤러를 포함합니다.
- `app/Models/`: 핵심 데이터 모델인 `Post`, `Tag`, `User`, `CommonCode`를 정의합니다.
- `app/Services/`: `PostService`와 같은 비즈니스 로직 서비스를 포함합니다.
- `app/Repositories/`: 데이터베이스 로직을 추상화하기 위해 리포지토리 패턴(`PostRepositoryInterface`, `EloquentPostRepository`)을 사용합니다.
- `routes/api/v1.php`: v1 API 엔드포인트를 정의합니다. 모든 v1 경로는 `/api/v1`로 접두사가 붙습니다.
- `database/migrations/`: 데이터베이스 스키마 정의를 포함합니다.

### 핵심 모델
- **`Post`**: 블로그 게시물을 위한 중앙 모델입니다. `uuid`, `user_id`, `title`, `slug`, `body`를 포함합니다.
- **`Tag`**: 게시물 분류를 위한 모델입니다.
- **`User`**: 작성자를 위한 사용자 모델입니다.
- **`CommonCode`**: 애플리케이션 전체에서 사용되는 공통 코드 또는 열거형을 관리하기 위한 모델입니다.

### API 및 라우팅
- API는 `v1` 접두사로 버전 관리됩니다.
- **인증**: `POST /api/v1/posts`와 같은 경로는 `auth:sanctum` 미들웨어에 의해 보호되며 유효한 API 토큰을 필요로 합니다.
- **공개 엔드포인트**: `GET /api/v1/base-data`와 같은 경로는 공개되며 기본 시스템 데이터를 제공합니다.

---

## ✅ 코드 품질 및 테스트

### 테스트
- **프레임워크**: **PHPUnit** (`^11.5.3`)은 유닛 및 기능 테스트에 사용됩니다.
- **구성**: `phpunit.xml`은 `tests/Unit` 및 `tests/Feature` 테스트 스위트를 별도로 실행하도록 구성되어 있습니다.
- **환경**: 테스트는 속도와 격리를 보장하기 위해 인메모리 SQLite 데이터베이스에 대해 실행됩니다.

### 정적 분석
- **도구**: **PHPStan** (`^2.1`)은 `phpstan.neon.dist`를 통해 구성된 정적 분석에 사용됩니다.
- **레벨**: 분석 레벨은 `5`로 설정되어 적당한 수준의 엄격함을 제공합니다.

### 코드 스타일
- **도구**: 일관된 코드 스타일을 적용하기 위해 **Laravel Pint**가 사용됩니다.
- **구성**: `.pint.json`은 `laravel` 프리셋을 기반으로 가져오기 정렬 및 단일 따옴표 사용을 위한 사용자 정의 규칙과 함께 스타일 규칙을 정의합니다.

---

## 🚀 스크립트 및 명령어

`composer.json`에 정의된 주요 스크립트:
- `composer dev`: `concurrently`를 사용하여 PHP 서버, 큐 워커, 로그 와처(`pail`), Vite 개발 서버를 동시에 실행하는 강력한 명령어입니다. 이것은 로컬 개발을 위한 기본 명령어입니다.
- `composer test`: 전체 PHPUnit 테스트 스위트를 실행합니다.
- `php artisan migrate`: 데이터베이스 마이그레이션을 실행합니다.
