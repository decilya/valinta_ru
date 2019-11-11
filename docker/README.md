# Установка докера на локальные машины
### Требования
1) Linux kernel версии 3.10 или выше
2) Docker Engine Версии 1.10 или выше
3) 2.00 GB Оперативной памяти
4) 3.00 GB свободного места на диске

### Установка докера
1) Нужно зарегистрироваться на [Docker](https://hub.docker.com)
2) Скачать и установить [Docker Desktop](https://hub.docker.com/?overlay=onboarding)

### Установка и запуск проекта Valinta
1) Поместите docker-compose.yml в корневой католог Valinta (рядом с composer.json)
2) В корневой папке, должен находится файл .htaccess с содержимым:

         Options +FollowSymLinks
         IndexIgnore */*
         RewriteEngine On
         
         RewriteCond %{REQUEST_URI} !^/(web)
         RewriteRule ^assets/(.*)$ /web/assets/$1 [L]
         RewriteRule ^css/(.*)$ web/css/$1 [L]
         RewriteRule ^js/(.*)$ web/js/$1 [L]
         RewriteRule ^images/(.*)$ web/images/$1 [L]
         RewriteRule (.*) /web/$1
         
         RewriteCond %{REQUEST_FILENAME} !-f
         RewriteCond %{REQUEST_FILENAME} !-d
         RewriteRule . /web/index.php
         
3) В папке /web должен лежать файл .htaccess с содержимым:

        RewriteEngine On RewriteBase /
        
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        
        RewriteRule . index.php
        
4) Открыть терминал, перейти в корневой каталог Valinta и ввести последовательно команды:
    + `docker-compose build`
    + `docker network create backend`
    + `docker-compose up -d`
5) После этого, все должно заработать. Проверить текущие запущениые контейнеры, можно командой `docker-compose ps`, 
а если имеются какие-то проблемы с запуском, логи можно посмотреть с помощью команды `docker-compose logs`


       