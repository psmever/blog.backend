<!DOCTYPE html>
<html lang="ko" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? config('app.name') }}</title>
  <!-- Tailwind CDN (dev용 빠른 적용) -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen flex flex-col overflow-hidden bg-gradient-to-b from-slate-50 to-white text-slate-800 antialiased">

  <!-- Header -->
  <header class="border-b bg-white/70 backdrop-blur supports-[backdrop-filter]:bg-white/50 flex-none">
    <div class="w-full px-6 py-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="h-8 w-8 rounded-xl bg-indigo-600"></div>
        <span class="font-semibold text-lg">{{ config('app.name') }}</span>
      </div>
      <nav class="hidden md:flex items-center gap-6 text-sm text-slate-600">
        <a href="#" class="hover:text-slate-900">Docs</a>
        <a href="#" class="hover:text-slate-900">API</a>
        <a href="#" class="hover:text-slate-900">Git</a>
      </nav>
    </div>
  </header>

  <main class="flex-1 overflow-hidden px-6 py-8 md:py-12">
    <div class="h-full flex flex-col items-center justify-center text-center max-w-3xl mx-auto">
      <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-100">
        Laravel Backend Base · Ready
      </span>
      <h1 class="mt-5 text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900">
        <span class="text-indigo-600">Psmever's Blog</span> 백엔드
      </h1>
      <p class="mt-5 text-slate-600 leading-relaxed">
        API · Web 라우터 분리, 베이스 컨트롤러, 일관 응답 포맷까지.
        지금 구조 위에 도메인만 얹으면 됩니다.
      </p>
      <div class="mt-8 flex flex-wrap justify-center gap-3">
        <a href="/api/v1/health"
           class="inline-flex items-center rounded-xl bg-indigo-600 px-5 py-3 text-white font-semibold shadow hover:bg-indigo-700">
          /api/v1/health 확인
        </a>
      </div>

      <div class="relative mt-10 w-full max-w-xl">
        <div class="absolute -inset-2 rounded-3xl bg-gradient-to-tr from-indigo-200 to-purple-200 blur-2xl opacity-60"></div>
        <div class="relative rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="rounded-2xl border p-4 text-left">
              <div class="text-slate-400">Environment</div>
              <div class="mt-1 font-semibold">{{ app()->environment() }}</div>
            </div>
            <div class="rounded-2xl border p-4 text-left">
              <div class="text-slate-400">Laravel</div>
              <div class="mt-1 font-semibold">{{ app()->version() }}</div>
            </div>
            <div class="rounded-2xl border p-4 text-left">
              <div class="text-slate-400">App Name</div>
              <div class="mt-1 font-semibold">{{ config('app.name') }}</div>
            </div>
            <div class="rounded-2xl border p-4 text-left">
              <div class="text-slate-400">Now</div>
              <div class="mt-1 font-semibold">{{ now()->toDateTimeString() }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="border-t flex-none">
    <div class="w-full px-6 py-6 text-sm text-slate-500">
      © {{ date('Y') }} {{ config('app.name') }} · All rights reserved.
    </div>
  </footer>
</body>
</html>
