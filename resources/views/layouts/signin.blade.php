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
            <router-view name="aboutMenu"></router-view>

            <template v-if="this.$route.path == '/'">
                <p>Сайт в автоматическом режиме шпионит за пользователями Вконтакте и предоставляет подробную
                информацию о том:</p>
                <ul>
                    <li>Сколько <b>времени</b> пользователь <b>сидит</b> Вконтакте</li>
                    <li>Кого пользователь <b>добавил или удалил из друзей</b> за выбранный период</li>
                </ul>
                <h5 class="mt-4">Графики активности пользователя в сети</h5>
                <p>Подробная информация о том, когда пользователь был в сети и сколько времени там провел. Информация
                представлена в виде <router-link :to="{ path: '/demo/visits' }">удобных графиков</router-link>, на
                которых можно выбрать период времени, за который отобразится информация (день, неделя, месяц),
                а также перемещаться назад\вперед, сравнивая активность пользователя за различные периоды времени.</p>
                <h5 class="mt-4">Отображение новых и удаленных друзей</h5>
                <p>Информация о том, кого пользователь <router-link :to="{ path: '/demo/friends' }">добавил в друзья</router-link>,
                а кого удалил. Причем, шпион запоминает время
                Вашего последнего визита, и если с тех пор, у пользователя произошли изменения,
                количество новых/удаленных друзей будет подсвечиваться напротив него в общем списке.</p>
                <p class="mt-5">Друзья, за которыми Вы начнете шпионить, никогда не узнают, что за ними следят. Сайт использует
                общедоступное API Вконтакте и с заданными интервалами времени опрашивают социальную сеть на наличие
                пользователя в сети и список его друзей.</p>
            </template>

            <template v-if="this.$route.path == '/demo/visits' || this.$route.path == '/demo/friends'">
                <div class="row text-center mt-5">
                    <div class="col text-center lead">
                        <h4>Пример статистики пользователя</h4>
                    </div>
                </div>
                <div class="py-3 ml-1" style="min-height: 70vh">
                    <router-view name="demoStatistic"></router-view>
                </div>
            </template>

            <template v-if="this.$route.path == '/about'">
                <p>После того как Вы авторизуетесь в приложении, перед Вами откроется список Ваших друзей ВКонтакте. Нажмите на интересующего Вас
                друга и нажмите кнопку "Следить". Как только Вы сделаете это, наш сайт начнет в автоматическом режиме с определенными интервалами
                времени опрашивать ВКонтакте на наличие этого пользователя в сети и изменения в списке его друзей. <b>Вернитесь</b> на сайт <b>через несколько
                часов</b> и Вы увидете статистику по Вашему другу!</p>
                <p>Пользователь не узнает, что Вы шпионите за ним, поскольку для слежки сайт использует общедоступное
                API Вконтакте и запрашивает информацию, которая доступна всем остальным. Но главное преимущество данного
                сервиса состоит в том, вам не нужно вручную заходить на страничку пользователя, чтобы узнать был он в сети
                или нет, а также держать в голове список его друзей и на память пытаться определить добавленных\удаленных друзей. Все
                это сайт сделает за вас в автоматическом режиме и предоставит подробную информацию, в который Вы нуждаетесь.</p>
            </template>

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
