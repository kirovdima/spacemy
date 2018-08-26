<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/css-new/bootstrap.min.css"/>
    <link rel="stylesheet" href="/css-new/bootstrap-grid.min.css"/>
    <link rel="stylesheet" href="/css-new/bootstrap-reboot.min.css"/>

    <link rel="stylesheet" type="text/css" href="/css-new/fullpage.css" />
    <script type="text/javascript" src="/js-lib/fullpage.js"></script>

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

    <title>Следить за друзьями</title>

    <style>
        #fp-nav ul li a span,
        .fp-slidesNav ul li a span {
            background-color: #66afe9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="top-menu" class="fixed-top bg-info text-white p-3">
            <div class="row">
                <div class="col text-center">
                    <a href="/login" class="btn btn-outline-light">Войти</a>
                </div>
            </div>
        </div>

        <div id="fullpage">
            <div class="section">
                <div class="row">
                    <div class="col text-center">
                        <h1>SPACEMY.RU</h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center">Следите за друзьями</div>
                </div>
                <div class="row mt-5 pt-5">
                    <div class="col text-center">
                        <small>Для слежения за пользователями мы используем публичное API ВКонтакте, что гарантирует Вам <strong>полную конфиденциальность</strong></small>
                    </div>
                </div>
            </div>



            <div class="section">
                <div class="row mt-2 mb-4">
                    <div class="col text-center"><h5>Графики активности пользователя ВКонтакте</h5></div>
                </div>
                <div class="row">
                    <div class="col">
                        <ul>
                            <li>Проверка пользователя на онлайн каждые <strong>10 минут</strong></li>
                            <li>Возможность просмотра активности по <strong>дням/неделям/месяцам</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col text-center">
                        <img class="mx-auto d-block d-md-none w-75" src="/images/graph.png">
                        <img class="mx-auto d-none d-md-block d-xl-none w-100" src="/images/graph-md.png">
                        <img class="mx-auto d-none d-xl-block w-100" src="/images/graph-xl.png">
                    </div>
                </div>
            </div>



            <div class="section">
                <div class="row mt-2 mb-4">
                    <div class="col text-center"><h5>Изменения в списках друзей</h5></div>
                </div>
                <div class="row">
                    <div class="col">
                        <ul>
                            <li>Вы всегда будете знать об <strong>добавленных/удаленных друзьях</strong> пользователя</li>
                        </ul>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col text-center">
                        <img class="mx-auto d-block d-md-none w-75" src="/images/friends.png">
                        <img class="mx-auto d-none d-md-block d-xl-none w-75" src="/images/friends-md.png">
                        <img class="mx-auto d-none d-xl-block w-75" src="/images/friends-xl.png">
                    </div>
                </div>
            </div>
        </div>
    </div>




    <script type="text/javascript">

        new fullpage('#fullpage', {
            //options here
            autoScrolling:true,
            scrollHorizontally: true,
            licenseKey: 'OPEN-SOURCE-GPLV3-LICENSE',
            navigation: true,
            showActiveTooltip: true,
            paddingTop: document.getElementById('top-menu').offsetHeight + 'px',
        });

        //methods
        fullpage_api.setAllowScrolling(true);
    </script>

</body>
</html>
