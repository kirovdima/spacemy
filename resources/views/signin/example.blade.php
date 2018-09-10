@extends('layouts.signin')
@section('content')
    <div class="row text-center mt-5">
        <div class="col text-center lead">
            <h4>Пример статистики пользователя</h4>
        </div>
    </div>
    <div class="py-3 ml-1" style="min-height: 70vh">
        <router-view name="demoStatistic"></router-view>
    </div>
@stop