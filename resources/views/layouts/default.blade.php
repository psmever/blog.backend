@include('includes.head')
@include('includes.header')
<main class="flex p-0 h-full items-center">
    <div class="flex h-full w-full flex-col items-center justify-center rounded-lg border border-gray-200 p-8">

        <div class="mt-8 text-center">
            <h1 class="text-4xl">Error</h1>
        </div>

        <button
                class="mt-8 block rounded-lg border border-green-700 bg-green-600 py-1.5 px-4 font-medium text-white transition-colors hover:bg-green-700 active:bg-green-800 disabled:opacity-50">
            Go
            Home
        </button>

    </div>
</main>
@include('includes.footer')
@include('includes.tail')
