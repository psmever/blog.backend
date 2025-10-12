# 🔐 Laravel Sanctum API Token 인증 프롬프트 모음 (한글)

## 🧱 기본 구조 이해

> Laravel Sanctum API 토큰 기반 인증의 전체 구조와 동작 원리를 단계별로 설명해줘.
> 로그인, 로그인 유지, 로그아웃, 토큰 만료가 각각 어떻게 동작하는지도 예시 코드로 보여줘.

---

## ⚙️ 로그인 API 생성

> Laravel 12 + Sanctum 환경에서 로그인 API(`/api/login`)를 작성해줘.
> 이메일/비밀번호를 검증하고, 성공 시 Personal Access Token을 발급하도록 구현해줘.
> 이전 토큰을 모두 삭제하고 새 토큰을 발급하도록 설정해줘.

---

## 🧾 인증 유저 조회

> 로그인된 사용자의 정보를 반환하는 `/api/v1/user` 엔드포인트를 만들어줘.
> `auth:sanctum` 미들웨어를 사용하고, 유효하지 않은 토큰일 때 401 응답을 반환하도록 작성해줘.

---

## 🚪 로그아웃 처리

> 현재 인증된 사용자의 토큰만 삭제하는 `/api/logout` API를 만들어줘.
> 성공 시 JSON 응답으로 `{ success: true, message: 'Logged out' }` 을 반환하게 해줘.

---

## ⏰ 토큰 만료 추가

> Sanctum의 기본 토큰에는 만료가 없으므로,
> `personal_access_tokens` 테이블에 `expires_at` 컬럼을 추가하고
> 토큰 발급 시 `now()->addHours(2)` 형태로 만료를 설정하도록 수정해줘.
> 만료 여부를 검사하는 `CheckTokenExpiry` 미들웨어도 함께 구현해줘.

---

## 💡 전체 예제 생성

> 위 모든 과정을 포함하는 `AuthController`, `routes/api.php`,
> 그리고 `CheckTokenExpiry` 미들웨어를 실제 작동 가능한 형태로 완성해줘.
> Laravel 12 버전에 맞춰 PSR-12 스타일로 작성하고, 응답은 `ApiResponseTrait` 형식을 따라줘.

---

## 🧰 추가 시나리오

> 다음 기능을 추가한 확장 버전을 만들어줘:
>
> -   모든 기기에서 로그아웃 (모든 토큰 삭제)
> -   토큰 재발급(`refresh` 엔드포인트)
> -   만료된 토큰 자동 삭제 스케줄러(Artisan Command)

---

## 🧩 테스트 코드

> Sanctum 인증 구조의 단위 테스트와 통합 테스트를 작성해줘.
> `/api/login`, `/api/logout`, `/api/v1/user` 가 각각 정상/에러 케이스를 통과하도록 만들어줘.
