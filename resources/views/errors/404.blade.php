<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>404 | {{ config('app.name') }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-b from-slate-50 to-white text-slate-700">
  <div class="text-center">
    <h1 class="text-9xl font-extrabold text-indigo-600">404</h1>
    <p class="mt-4 text-xl font-semibold">페이지를 찾을 수 없습니다.</p>
    <p class="mt-2 text-slate-500">{{ $message ?? '요청하신 페이지가 존재하지 않거나 이동되었습니다.' }}</p>

    <div class="mt-8">
      <a href="/"
         class="rounded-xl bg-indigo-600 px-6 py-3 text-white font-semibold shadow hover:bg-indigo-700">
        홈으로 돌아가기
      </a>
    </div>
  </div>

  <footer class="mt-16 text-sm text-slate-400">
    © {{ date('Y') }} {{ config('app.name') }}
  </footer>
</body>
</html>
