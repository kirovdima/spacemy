<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ $api_token }}">

    <link rel="stylesheet" href="/css-new/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css-new/bootstrap-grid.min.css"/>
    <link rel="stylesheet" href="/css-new/bootstrap-reboot.min.css"/>

    <title>Следить за друзьями</title>
</head>
<body>
    <div id="app">
        <div class="container">
            <router-view name="menu"></router-view>
            <div class="py-3">
                <router-view name="friends"></router-view>
                <router-view name="statistic"></router-view>
            </div>
        </div>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>
</html>
