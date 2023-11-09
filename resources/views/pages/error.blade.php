@extends('layouts.blank')
@section('content')
    <div class="mt-8 text-center">
        <div class="text-4xl">{{$message ?? '문제가 발생했습니다.'}}</div>
        @if (env('APP_ENV') != 'Production')
            <div class="text-1xl pt-2">message: {{$error['message']}}</div>
        @endif
    </div>
@stop
