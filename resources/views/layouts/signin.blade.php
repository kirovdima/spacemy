<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

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

    <title>Следить за друзьями</title>
</head>
<body>
    <div class="container d-flex justify-content-center" style="height: 100vh; width: 100vw;">
        <div class="align-self-center">
            <div class="text-center">
                <h3>SPACEMY.RU</h3>
            </div>
            <div class="text-center">
                Следите за друзьями
            </div>
            <div class="text-center my-5">
                <a href="/login" class="btn btn-success">Войти</a>
            </div>
            <div class="text-center mt-5 my-2 pt-lg-5">
                <small>Для слежения за пользователями мы используем публичное API ВКонтакте, что гарантирует Вам <strong>полную конфиденциальность</strong></small>
            </div>
        </div>
    </div>
</body>
</html>
