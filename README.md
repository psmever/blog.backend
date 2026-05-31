# 📘 Blog Backend (Laravel 12)

> **Next.js + Laravel Blog Project** 의 백엔드 서비스입니다.
> Docker 환경에서 Laravel 12.x 기반으로 작동하며,
> `Pint`, `PHPStan`, `Larastan` 을 이용해 코드 품질과 일관성을 유지합니다.

---

## 🧱 프로젝트 구조

```text
blog.backend/
├── app/                  # 주요 Laravel 앱 로직
├── bootstrap/            # 초기 부트스트랩 로직
├── config/               # 환경별 설정 파일
├── database/             # 마이그레이션 및 시더
├── public/               # 퍼블릭 엔드포인트
├── routes/               # 웹 / API 라우트
│   ├── api/
│   │   ├── api.php
│   │   └── v1.php
│   └── web/
│       ├── web.php
│       └── admin.php
├── storage/              # 캐시, 로그, 업로드
│   └── phpstan/          # PHPStan 캐시 (Git 무시)
├── tests/                # 테스트 코드
├── .env.example          # 환경 변수 예시
├── composer.json         # PHP 의존성
├── phpstan.neon.dist     # PHPStan / Larastan 설정
├── pint.json             # Laravel Pint 설정
└── README.md             # ← 현재 문서
```

---

## ⚙️ 개발 환경

이 백엔드는 `blog.workspace` 디렉터리의 Docker Compose 환경에서 실행됩니다.

### 1️⃣ 컨테이너 실행

```bash
cd ../blog.workspace
make up local
```

- `backend` : Laravel Backend
- `database` : MariaDB Database
- `frontend` : Next.js Frontend
- `.env.example`을 참고해 `.env`를 직접 작성하여 환경 구성

### 2️⃣ Artisan 명령어 실행

```bash
make migrate
make seed
make logs
```

또는 직접 실행:

```bash
./scripts/artisan.sh migrate
```

---

## 🧩 라우팅 구조

| 구분     | 경로           | 설명                 |
| -------- | -------------- | -------------------- |
| Web      | `/`            | 메인 페이지          |
| API      | `/api/health`  | Health Check         |
| API Demo | `/api/_demo/*` | 예시용 테스트 라우트 |

라우트 캐시:

```bash
php artisan route:cache
```

---

## 🧰 코드 품질 도구

### 🧹 **Laravel Pint**

> Laravel 공식 코드 스타일러 (Prettier + PSR-12 기반)

#### 실행 (Pint)

```bash
./vendor/bin/pint
```

#### VSCode 자동 실행

- 저장 시 자동 포맷 (`open-southeners.laravel-pint` 확장 사용)
- 설정: `.vscode/settings.json`

---

### 🧠 **PHPStan + Larastan**

> 코드의 타입 안정성과 논리 오류를 정적으로 분석

#### 실행 (PHPStan)

```bash
./vendor/bin/phpstan analyse
```

#### 설정 파일

📄 `phpstan.neon.dist`

```neon
includes:
  - ./vendor/nunomaduro/larastan/extension.neon

parameters:
  paths:
    - app
  level: 5
  ignoreErrors:
    - '#Call to an undefined method Illuminate\\.*#'
    - '#Property .* does not exist on .*#'
  reportUnmatchedIgnoredErrors: false
  tmpDir: storage/phpstan
```

#### 캐시 초기화

```bash
./vendor/bin/phpstan clear-result-cache
```

---

## 💻 VSCode 권장 설정

📄 `.vscode/settings.json`

```json
{
    "editor.formatOnSave": true,

    "[php]": {
        "editor.defaultFormatter": "open-southeners.laravel-pint"
    },

    "phpstan.executablePath": "${workspaceFolder}/vendor/bin/phpstan",
    "phpstan.configFile": "${workspaceFolder}/phpstan.neon.dist",
    "phpstan.runOnSave": true
}
```

📦 주요 확장 목록:

- `open-southeners.laravel-pint` (Laravel Pint)
- `sanderronde.phpstan-vscode` (PHPStan)
- `bmewburn.vscode-intelephense-client` (PHP 인텔리전스)

---

## 🧑‍💻 개발 흐름 예시

```bash
# 컨테이너 시작
make up local

# DB 마이그레이션
make migrate

# 코드 자동 포맷 (Pint)
./vendor/bin/pint

# 코드 정적 분석 (PHPStan)
./vendor/bin/phpstan analyse
```

---

## 🚫 Git 제외 파일

📄 `.gitignore`

```gitignore
/storage/phpstan/
/vendor/
/node_modules/
/.env
/.env.*
```

---

## 🩺 헬스체크

```bash
curl http://localhost:4000/api/health
# → { "success": true, "message": "ok", "data": { ... } }
```

---

## 🏁 마무리

이 백엔드는 다음을 목표로 설계되었습니다:

- Docker 기반 완전 격리 개발 환경
- Laravel 12.x 최신 구조 준수
- 코드 품질 자동화 (Pint + PHPStan)
- VSCode에서 자동 포맷 + 실시간 분석

---

### 👤 Maintainer

**@sm**
📍 Development Workspace: `/Users/sm/Workspaces/Development/MyProject/blog/blog.backend`
