# Backend 저장소 가이드

## 기본 원칙

- 모든 대화와 작업 보고는 한글로 작성한다.
- 이 저장소는 Laravel 백엔드 애플리케이션 전용이다.
- 프런트엔드 UI 수정은 `../blog.frontend`에서, Docker/배포/공용 스크립트 수정은 `../blog.workspace`에서 진행한다.

## 작업 범위

- 수정 대상:
  - `app/`
  - `routes/`
  - `database/`
  - `tests/`
  - `resources/`
  - `public/`
- 기본적으로 수정하지 않는 대상:
  - `../blog.frontend`
  - `../blog.workspace`

## 자주 쓰는 명령

- 로컬 전체 스택 시작: `cd ../blog.workspace && make up`
- Artisan 실행: `cd ../blog.workspace && ./scripts/artisan.sh route:list`
- 마이그레이션: `cd ../blog.workspace && make migrate`
- 시더 실행: `cd ../blog.workspace && make seed`
- 테스트: `php artisan test` 또는 `composer test`
- 코드 포맷: `./vendor/bin/pint`

## 코딩 규칙

- PHP는 PSR-12를 따른다.
- 클래스는 `StudlyCase`, 메서드는 `camelCase`, 마이그레이션 파일은 `snake_case`를 사용한다.
- 컨트롤러는 `*Controller` 접미사를 유지한다.
- 라우트는 버전별 파일로 나누고, 도메인 로직은 `app/` 내부에서 응집되게 유지한다.

## 테스트와 검증

- 변경이 있으면 최소한 관련 `php artisan test` 또는 대상 테스트를 실행한다.
- DB 동작을 건드리면 마이그레이션과 시더 영향까지 함께 확인한다.
- API 계약이 바뀌면 요청/응답 예시 또는 문서 변경을 같이 반영한다.

## 안전 규칙

- `.env`, `.env.*`, `*.enc` 파일은 꼭 필요한 경우에만 열람하거나 수정한다.
- 컨테이너/배포 흐름 변경이 필요하면 먼저 `../blog.workspace` 수정이 맞는지 판단한다.
- 한 요청이 여러 저장소에 걸치면 백엔드 변경과 그 외 변경을 분리해서 커밋한다.

## 커밋 규칙

- 커밋 메시지는 짧은 한 줄 한국어 요약을 우선한다.
- 백엔드 변경만 담긴 커밋을 유지하고, 프런트/워크스페이스 변경과 섞지 않는다.
