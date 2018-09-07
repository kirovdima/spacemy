# Шпион ВК
http://spacemy.ru - сайт, позволяющий "следить" за профилями друзей Вконтакте

## Структура проекта
* app/Console/Commands
  * CheckFriendsStatus - проверяет онлайн/неонлайн друзья (генерирует задачи для очереди check_user_friends_status)
  * CheckFriendsStatus - проверяет изменения в списках друзей (генерирует задачи для очереди check_user_friends_status)
* app/Http/Controllers - контроллеры
* app/Http/Requests - валидаторы контроллеров
* app/Jobs - джобы для раббита
* app/MongoModels - модели MongoDB
* app/Services - вспомогательные сервисы

* resources/assets/js - папка Vue.js

### /etc/supervisor/conf.d/laravel-worker.conf
```
[program:check_user_friends_status]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/spacemy/artisan queue:work --queue=check_user_friends_status --sleep=3 --tries=3
autostart=true
autorestart=true
user=kirov
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/spacemy/storage/logs/worker.log

[program:check_user_friends_list]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/spacemy/artisan queue:work --queue=check_user_friends_list   --sleep=3 --tries=3 --timeout=300
autostart=true
autorestart=true
user=kirov
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/spacemy/storage/logs/worker.log

[group:laravel-workers]
programs=check_user_friends_status,check_user_friends_list

```
