<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8" />
    <title>:: {{ env('APP_NAME')}} ::</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body>
    <div class="h-screen flex flex-col">
        <header class="p-0">
            <nav class="bg-white border-gray-200 dark:bg-gray-900">
                <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                    <span
                        class="self-center text-2xl font-semibold whitespace-nowrap text-white cursor-pointer">NicePage</span>
                </div>
            </nav>
        </header>
        <main class="flex p-0 h-full items-center">
            <div class="flex h-full w-full flex-col items-center justify-center rounded-lg border border-gray-200 p-8">

                <div class="mt-8 text-center">
                    <h1 class="text-4xl">Welcome to {{ env('APP_NAME')}}</h1>
                </div>

                <button
                    class="mt-8 block rounded-lg border border-green-700 bg-green-600 py-1.5 px-4 font-medium text-white transition-colors hover:bg-green-700 active:bg-green-800 disabled:opacity-50">Get
                    started</button>

            </div>
        </main>
        <footer class="bg-white m-4">
            <div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
                <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2023 Nicepage™. All Rights
                    Reserved.
                </span>
                <ul
                    class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
                    <li>
                        <a href="javascript:;" class="hover:underline">Laravel
                            v{{ Illuminate\Foundation\Application::VERSION }}
                            (PHP v{{ PHP_VERSION }}) Environment:
                            {{ env("APP_ENV") }}</a>
                    </li>
                </ul>
            </div>
        </footer>
    </div>
</body>
</html>
