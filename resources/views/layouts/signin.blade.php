<!doctype html>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="description" content="Шпион вконтакте, следить за друзьями, статистика пользователя вконтакте">
    <meta name="keywords" content="Шпион вконтакте, следить за друзьями, активность пользователя, изменения в списке друзей, статистика пользователя вконтакте">
    <meta http-equiv="Content-Language" content="ru">
    <meta name="robots" content="ALL,index,follow">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ $api_token }}">
    <meta name="random-person-id" content="{{ $random_person_id }}">
    <meta name="yandex-verification" content="144ebb06bc676d16" />

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
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
            <div class="row mt-5 ml-1 border-left border-success border-3">
                <div class="col">
                    <h5 class="mb-4">Информация об активности пользователя</h5>
                    <p>Подробная стастистика о том, сколько часов/минут пользователь сидел ВКонтакте в виде удобных графиков
                        по дням/неделям/месяцам</p>
                </div>
            </div>
            <div class="row mt-5 ml-1 border-left border-success border-3">
                <div class="col">
                    <h5 class="mb-4">Изменения в списке друзей</h5>
                    <p>Отображение всех изменений в списке друзей интересующего Вас пользователя с указанием даты, когда произошло событие</p>
                </div>
            </div>
            <div class="row text-center mt-5">
                <div class="col text-center lead">
                    <h4>Пример статистики пользователя</h4>
                </div>
            </div>
            <div class="py-3 ml-1" style="min-height: 70vh">
                <router-view name="demoStatistic"></router-view>
            </div>
            <div class="row mt-5 ml-1 border-left border-info border-3">
                <div class="col">
                    <h5 class="mb-4">Как шпионить с помощью данного сайта?</h5>
                    <p>После того как Вы авторизуетесь в приложении, перед Вами откроется список Ваших друзей ВКонтакте. Нажмите на интересующего Вас
                    друга и нажмите кнопку "Следить". Как только Вы сделаете это, наш сайт начнет в автоматическом режиме с определенными интервалами
                    времени опрашивать ВКонтакте на наличие этого пользователя в сети и изменения в списке его друзей. <b>Вернитесь</b> на сайт <b>через несколько
                    часов</b> и Вы увидете статистику по Вашему другу!</p>
                </div>
            </div>
            <div class="row my-5">
                <div class="col text-center">
                    <a href="/login" class="btn btn-outline-info">Следить за друзьями</a>
                </div>
            </div>
            <router-view name="footer"></router-view>
        </div>
    </div>
</body>
<script src="{{ asset('js/app.js') }}"></script>
</html>
