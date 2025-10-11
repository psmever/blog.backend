<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>500 | {{ config('app.name') }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-b from-rose-50 to-white text-slate-700">
  <div class="text-center">
    <h1 class="text-9xl font-extrabold text-rose-600">500</h1>
    <p class="mt-4 text-xl font-semibold">서버에 문제가 발생했습니다.</p>
    <p class="mt-2 text-slate-500">{{ $message ?? '일시적인 오류입니다. 잠시 후 다시 시도해 주세요.' }}</p>

    <div class="mt-8 flex flex-wrap justify-center gap-3">
      <a href="/"
         class="rounded-xl bg-rose-600 px-6 py-3 text-white font-semibold shadow hover:bg-rose-700">
        홈으로 돌아가기
      </a>
      <a href="mailto:support@example.com"
         class="rounded-xl border border-slate-200 px-6 py-3 font-semibold text-slate-700 hover:bg-slate-50">
        관리자에게 문의
      </a>
    </div>
  </div>

  <footer class="mt-16 text-sm text-slate-400">
    © {{ date('Y') }} {{ config('app.name') }}
  </footer>
</body>
</html>
