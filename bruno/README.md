# Blog Backend Bruno 컬렉션

## 시작하기

1. Bruno에서 `bruno/` 디렉터리를 컬렉션으로 엽니다.
2. `local` 환경을 선택합니다.
3. 환경 변수에서 `userEmail`, `userPassword`를 입력합니다.
4. `auth/로그인` 요청을 실행합니다.

로그인에 성공하면 `accessToken`, `refreshToken`이 현재 Bruno 환경에 자동으로 저장됩니다. UUID 발급과 게시글 생성 요청도 각각 `postUuid`, `postSlug`를 자동 갱신합니다.

## 환경 변수

| 변수 | 설명 |
| --- | --- |
| `baseUrl` | Laravel 애플리케이션 URL. 기본값은 `http://localhost` |
| `clientType` | 모든 `/api/*` 요청에 필요한 `Client-Type`. 현재 테스트 클라이언트 코드는 `CT04P` |
| `userEmail` | 로그인 이메일. Bruno의 로컬 비밀 저장소에서 관리 |
| `userPassword` | 로그인 비밀번호. Bruno의 로컬 비밀 저장소에서 관리 |
| `accessToken` | 로그인·갱신 응답에서 자동 설정되는 액세스 토큰 |
| `refreshToken` | 로그인·갱신 응답에서 자동 설정되는 리프레시 토큰 |
| `postUuid` | UUID 발급 응답에서 자동 설정되는 게시글 UUID |
| `postSlug` | 게시글 생성·저장·게시 응답에서 자동 설정되는 공개 슬러그 |

`vars:secret`로 선언한 값은 환경 파일에 기록되지 않습니다. 실제 계정 정보나 토큰을 일반 변수로 바꾸거나 저장소에 직접 작성하지 마세요.

## 요청 실행 순서

게시글 쓰기 흐름은 다음 순서를 권장합니다.

1. `auth/로그인`
2. `v1/posts/UUID 발급`
3. `v1/posts/게시글 생성`
4. `v1/posts/게시글 임시 저장`
5. `v1/posts/게시글 게시`

이미지 업로드 요청의 파일 입력은 Bruno 화면에서 로컬 이미지 파일을 직접 선택해야 합니다.

## CLI

Bruno CLI가 설치되어 있다면 컬렉션 루트에서 실행할 수 있습니다.

```bash
cd bruno
bru run --env local
```

전체 컬렉션 실행에는 로그아웃 요청도 포함되므로, 개발 중에는 개별 요청이나 필요한 폴더만 실행하는 편이 안전합니다.
