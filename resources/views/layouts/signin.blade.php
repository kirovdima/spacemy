<!doctype html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="@yield('description')">
    <meta name="keywords" content="@yield('keywords')">
    <meta http-equiv="Content-Language" content="ru">
    <meta name="robots" content="ALL,index,follow">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ $api_token }}">
    <meta name="random-person-id" content="{{ $random_person_id }}">
    <meta name="yandex-verification" content="144ebb06bc676d16" />

    <meta property="og:title" content="Шпион ВКонтакте | Статистика активности | Новые и удаленные друзья" />
    <meta property="og:site_name" content="Шпион ВКонтакте" />
    <meta property="og:description" content="Сайт шпионит за вашими друзьями ВКонтакте и показывает: сколько времени пользователь находился в сети, его новых и удаленных друзей." />
    <meta property="og:type" content="website" />

    <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
    <meta name="google-site-verification" content="XlTG1olQN5UJA-UAi57i4eEw6H4bMYiyQQ8uo1vl2oI" />

    <link rel="stylesheet" href="/css-new/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css-new/bootstrap-grid.min.css"/>
    <link rel="stylesheet" href="/css-new/bootstrap-reboot.min.css"/>

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function (d, w, c) {
            (w[c] = w[c] || []).push(function() {
                try {
                    w.yaCounter49091602 = new Ya.Metrika2({
                        id:49091602,
                        clickmap:true,
                        trackLinks:true,
                        accurateTrackBounce:true,
                        webvisor:true
                    });
                } catch(e) { }
            });

            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function () { n.parentNode.insertBefore(s, n); };
            s.type = "text/javascript";
            s.async = true;
            s.src = "https://mc.yandex.ru/metrika/tag.js";

            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f, false);
            } else { f(); }
        })(document, window, "yandex_metrika_callbacks2");
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/49091602" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <title>Шпион ВКонтакте</title>
    <style>
        .border-3 {
            border-width: 3px !important;
        }
    </style>
</head>
<body>
    <div id="app">
        <div class="container">
            <div class="row fixed-top bg-info text-white p-3">
                <div class="col text-center">
                    <a href="/login" class="btn btn-outline-light">Следить за друзьями</a>
                </div>
            </div>
            <div class="row my-5">
                <div class="col"></div>
            </div>
            <div class="row mt-5">
                <div class="col text-center">
                    <h2>Шпион ВКонтакте</h2>
                </div>
            </div>
            <div class="row text-center">
                <div class="col">
                    Следите за своими друзьями
                </div>
            </div>
            @include('signin.menu')

            @yield('content')

            <div class="row my-5">
                <div class="col text-center">
                    <a href="/login" class="btn btn-outline-info">Следить за друзьями</a>
                </div>
            </div>

            @include('signin.footer')
        </div>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>
</html>
