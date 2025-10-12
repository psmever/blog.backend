# ✅ Blog Backend Development Checklist

> Laravel 12 기반 Blog Backend 개발 진행표
> Last Updated: 2025-10-11

---

## 🧱 1. 환경 구성

- [x] Laravel 12 프로젝트 초기화
- [x] Docker 개발 환경 연결 (`blog.docker`)
- [x] Makefile / Artisan 헬퍼 스크립트 구성
- [x] Laravel Pint 포맷터 설치 및 VSCode 연동
- [x] PHPStan + Larastan 정적 분석기 설정
- [x] `phpstan.neon.dist` 설정 및 캐시 디렉토리 무시 처리
- [x] `README.md` 작성 (개발 환경 문서)
- [x] Client-Type 헤더 검증 미들웨어 추가

---

## 🧩 2. API 베이스 구축

- [x] `ApiBaseController` / `WebBaseController` 생성
- [x] `ApiResponseTrait` / `WebResponseTrait` 구조화
- [x] 표준 응답 포맷 (`success`, `message`, `data`) 정의
- [x] 예외 처리기 (`ApiExceptionHandler`) 구성
- [x] `/api/health` 엔드포인트 정상 응답 확인
- [x] `/api/v1/demo` 라우트 확장 (테스트용)

---

## 🔐 3. 인증 (Auth)

- [ ] Laravel Sanctum 또는 JWT 패키지 설치
- [ ] 로그인 / 로그아웃 API 구성
- [ ] 인증 미들웨어 적용
- [ ] 인증 테스트
- [ ] Model / Repository / Service 구조 도입

---

## 🧱 4. 서비스 계층 설계

- [x] 공통 코드 테이블 추가
- [ ] Post 모델 및 마이그레이션 생성
- [ ] Repository 계층 설계 및 의존성 주입 확인
- [ ] 단위 테스트 추가 (`php artisan test`)

---

## 🌍 5. 배포 및 환경 관리

- [ ] `.env.example` 업데이트 (설정 정리)
- [ ] production 환경용 `.env.production.enc` 생성
- [ ] `make up production` 환경 점검
- [ ] Docker 이미지 빌드 및 배포 테스트

---

## 🧠 6. 품질 자동화

- [x] Pint 자동 포맷 VSCode 연동 확인
- [x] PHPStan `level 5` 기준 분석 통과
- [ ] GitHub Actions CI 구성 (Pint + PHPStan)
- [ ] 테스트 커버리지 리포트 추가

---

## 📊 진행 현황

> ✅ 완료: 17 / 31 항목
> 🔄 진행률: **55%**

---

## 🗓️ 변경 로그

| 날짜       | 내용                                  |
| ---------- | ------------------------------------- |
| 2025-10-11 | 초기 checklist 작성 및 환경 구축 완료 |
| 2025-10-11 | API Trait 및 예외 처리 구조 완료      |
