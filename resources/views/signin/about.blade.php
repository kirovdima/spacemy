@extends('layouts.signin')
@section('content')
    <div class="row mb-3">
        <div class="col">
            <h2>Как работает шпион вконтакте?</h2>
        </div>
    </div>

    <h4>Основные составляющие шпиона вконтакте:</h4>
    <ul>
        <li>Авторизация</li>
        <li>Регулярный опрос Вконтакте на наличие изменений, касающихся отслеживаемого пользователя</li>
        <li>Хранение истории всех изменений</li>
        <li>Отображение статистики активности и изменения списка друзей</li>
    </ul>

    <h5>Авторизация</h5>
    <p>Для того, чтобы слежка за пользователем стала возможной, вам нужно авторизоваться вконтакте через приложение.
    В этот момент, шпион вконтакте получает от социальной сети ключ авторизации в АПИ (не пароль!), с помощью которого
        будет делать специальные запросы для проверки отлслеживаемого пользователя на наличие изменений.</p>

    <h5>Опрос Вконтакте на наличие изменений</h5>
    <p>Каждые 10 минут шпион с помощью вашего ключа авторизации в АПИ обращается к Вконтакте и проверяет находится ли
    пользователь в сети. Каждый час запрашивает список его друзей.</p>

    <h5>История изменений</h5>
    <p>Шпион сохраняет все изменения статуса пользователя и списка его друзей, если таковые имеются.</p>

    <h5>Отображение статистики активности и изменения списка друзей</h5>
    <p>И наконец, шпион по накопленным у него данным о пользователе, в удобном для вас виде отображает статистику его
    активности и все изменения списка друзей.</p>
@stop
@section('description', 'Шпион вконтакте, отображение статистики активности и изменения списка друзей')
@section('keywords', 'Шпион вконтакте, отображение статистики активности и изменения списка друзей')