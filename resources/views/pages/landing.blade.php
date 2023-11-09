@extends('layouts.blank')
@section('content')
    <div class="mt-8 text-center">
        <h1 class="text-4xl">Welcome to {{ env('APP_NAME')}}</h1>
    </div>

    <button
            class="mt-8 block rounded-lg border border-green-700 bg-green-600 py-1.5 px-4 font-medium text-white transition-colors hover:bg-green-700 active:bg-green-800 disabled:opacity-50">
        Get
        started
    </button>
@stop
