# 🤖 VSCode Codex 한글 개발 프롬프트 모음 (Laravel 백엔드용)

> Laravel 12 + Docker + API 개발 환경에 최적화된 명령 템플릿입니다.
> VSCode ChatGPT 확장, Cursor, Copilot 등에서 바로 사용할 수 있습니다.

---

## 🧱 컨트롤러 / 서비스 계층

### 🧩 컨트롤러 리팩터링

> 이 컨트롤러의 로직을 서비스 계층으로 분리해줘.
> Repository / Service 구조로 나누고, 예외 처리를 추가해줘.

### 💡 새 컨트롤러 생성

> `/api/v1/posts` 전용 컨트롤러를 만들어줘.
> index(), show(), store(), update(), destroy() 메서드 포함하고
> `ApiResponseTrait` 형식으로 응답하게 해줘.

### 📜 주석 추가

> 이 컨트롤러 전체에 PHPDoc 주석을 추가해줘.
> 각 메서드에 요청 파라미터와 반환값을 설명해줘.

---

## 🧩 Request / Validation

### ✅ FormRequest 생성

> 이 요청에 대한 Laravel FormRequest 클래스를 만들어줘.
> validation 규칙과 에러 메시지도 포함해줘.

### ⚙️ Validation 리팩터링

> 컨트롤러 내부의 validate() 호출을 별도의 Request 클래스로 분리해줘.

---

## 💬 API 응답 / Trait

### 🧠 Trait 개선

> `ApiResponseTrait`을 PSR-12 규칙에 맞게 리팩터링하고,
> PHP 타입힌트와 PHPDoc을 추가해줘.

### 🧩 응답 구조 통일

> API 응답을 `{ success, message, data }` 형태로 표준화해줘.
> 모든 에러는 예외 처리기(`ApiExceptionHandler`)를 통해 일관성 있게 반환되게 해줘.

---

## 🧰 예외 처리 / 로그

### 🚨 예외 핸들러 개선

> `Handler.php`에서 API 예외(`ApiException`)와 일반 예외를 구분해서
> JSON 응답으로 반환하도록 수정해줘.

### 📊 로그 정리

> `storage/logs/laravel.log`의 최근 에러를 분석하고,
> 중복 로그 패턴을 줄이는 설정을 제안해줘.

---

## 🧱 모델 / 서비스 / 리포지토리

### 🧱 모델 생성

> `Post` 모델을 만들고 `fillable` 필드로 title, content, author_id를 추가해줘.

### 💾 리포지토리 생성

> PostRepository 클래스를 만들어 CRUD 메서드를 추가해줘.
> DB 접근은 Eloquent로 처리해줘.

### 🧠 서비스 계층

> PostService 클래스를 생성해서
> Repository를 주입받고 비즈니스 로직을 처리하도록 만들어줘.

---

## 🧩 테스트

### 🧪 PHPUnit 테스트 생성

> `PostController`에 대한 PHPUnit 테스트 클래스를 생성해줘.
> 각 메서드에 대해 정상/에러 케이스를 포함해줘.

### 🧠 Mocking 예제

> Repository를 Mock으로 대체한 서비스 테스트 코드를 작성해줘.

---

## 🐳 Docker / 환경 설정

### 🧩 Docker Compose 점검

> docker-compose.yml 구성을 검토하고,
> PHP-FPM과 Node 컨테이너의 볼륨, 포트, env 설정을 확인해줘.

### 🧱 Makefile 리팩터링

> Makefile에 `make pint`, `make phpstan` 명령을 추가해줘.
> 실행 시 컨테이너 내부에서 작동하게 구성해줘.

---

## 📜 문서화 / 자동화

### 📘 API 문서 초안

> 현재 컨트롤러를 기준으로 Swagger/OpenAPI 문서 초안을 작성해줘.
> 경로, 메서드, 요청/응답 구조 포함.

### 🧩 README 생성

> 현재 Laravel 프로젝트의 구조와 개발 환경, 사용 명령어를
> 한글로 정리한 README.md를 생성해줘.

### 🧠 TASKS.md 업데이트

> 현재 진행 중인 항목을 체크하고, 완료된 항목을 업데이트해줘.
> 진행률을 계산해서 표시해줘.

---

## 🔍 디버깅 / 에러 분석

### 🧾 에러 로그 분석

> 이 예외 메시지를 분석하고, 원인과 해결 방안을 단계별로 알려줘.

### ⚙️ 라우팅 점검

> `routes/api.php`와 `RouteServiceProvider` 구성을 분석해서
> “Target class [web] does not exist” 에러의 원인을 찾아줘.

---

## 🧠 번역 / 설명 / 학습

### 📘 코드 설명

> 아래 코드를 한국어로 설명해줘.
> 주요 변수와 로직 흐름을 중심으로 이해하기 쉽게 정리해줘.

### 🧠 문법 도움

> Laravel 12 버전에서 `Route::apiResource`의 사용법을 예시와 함께 설명해줘.
