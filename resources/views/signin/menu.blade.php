<ul class="nav nav-pills my-3">
    <li class="nav-item">
        <a href="/" class="nav-link {{ Route::currentRouteName() == 'main' ? 'active' : '' }}">Шпион вконтакте</a>
    </li>
    <li class="nav-item">
        <a href="/demo/visits" class="nav-link {{ in_array(Route::currentRouteName(), ['demo_visits', 'demo_friends']) ? 'active' : '' }}">Пример статистики</a>
    </li>
    <li class="nav-item">
        <a href="/about" class="nav-link {{ Route::currentRouteName() == 'about' ? 'active' : '' }}">Как работает шпион вконтакте?</a>
    </li>
</ul>