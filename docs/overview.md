# Blog Backend Overview

## Purpose & Scope

- Laravel 12 기반 블로그 백엔드 서비스로, Next.js 프런트엔드(`blog.frontend`)와 연동되는 API 계층을 제공합니다.
- Docker Compose 프로젝트(`blog.docker`)에서 PHP-FPM, MariaDB, Node 컨테이너와 함께
  구동되며, 로컬 개발과 배포 전 테스트를 목표로 합니다.
- `docs/TASKS.md`(개발 진행표)와 연계하여 현재 작업 상황과 우선순위를 공유합니다.

## 상위 프로젝트 구조

```text
blog/
 ├── blog.backend/       # Laravel 백엔드 (현재 위치)
 ├── blog.frontend/      # Next.js 프런트엔드
 └── blog.docker/        # Docker 설정 및 실행 스크립트
```

- 본 문서는 `blog.backend` 디렉터리를 기준으로 작성되었습니다.
- 공용 스크립트 및 컨테이너 관리는 `blog.docker` 디렉터리에서 이뤄집니다.

## 기술 스택

- `PHP 8.2` + `Laravel 12.x`
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

- `cd ../blog.docker && make up local` : 로컬 Docker 서비스 기동
- `make down`, `make logs` 등 보조 명령도 동일 디렉터리에서 실행합니다.

### Artisan 명령 실행

- 기본 형태: `php artisan <command>` (예: `php artisan migrate`)
- Docker 스크립트 이용:
  - `blog.docker/scripts/artisan.sh <command>`
  - `blog.backend` 기준 상대 경로 예시: `../blog.docker/scripts/artisan.sh migrate`
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

## 문서 & 참고

- 개발 진행 상황: `docs/TASKS.md`
- 환경 변수 정의: `.env.example` (생성/암호화된 환경 파일은 `blog.docker`에서 관리)
- 향후 작성 예정: 배포 전략, 인증/서비스 계층 설계 문서

필요 시 본 문서에 추가 섹션(예: ERD, 시퀀스 다이어그램, CI 파이프라인)을 만들어 협업 중 발견한 내용을 계속 보완해 주세요.
