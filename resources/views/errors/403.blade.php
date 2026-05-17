<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>403 | 접근이 제한되었습니다</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-8xl font-bold text-red-500">403</h1>
        <p class="mt-4 text-2xl font-semibold">접근이 제한되었습니다</p>
        <p class="mt-2 text-gray-500">{{ $message ?? '이 페이지를 볼 권한이 없습니다.' }}</p>
        <a href="{{ route('home') }}"
           class="mt-6 inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
           홈으로 돌아가기
        </a>
    </div>
</body>
</html>
