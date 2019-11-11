--PHP--

Установить пакет php-intl

--Скачать и Установить Composer в корень сайта--

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"

--Установить зависимости--

php composer.phar install
php composer.phar update

В процессе установки могут попросить токен с Github.

--После установки Yii2--
Скопировать файл /config/origin/db.php в /config/db.php и прописать необходимую БД
Скопировать файл /config/origin/params.php в /config/params.php
Скопировать файл /config/origin/web.php в /config/web.php

--Устоновить права на папки--
chown -R apache web/assets/ runtime/

--Опции (params.php)--

enableJivosite				- Показывать Jivosite
enableYandexCounter			- Включить счетчик Яндекса
defaultSorting[filter] 		- Сортировка по умолчанию
defaultSorting[direction] 	- Направление сортировки по умолчанию
remindMessageDivisibleByDays- Если у юзера отсутствие активности в днях на сайте кратно этому числ, ему отправляется напоминание.
searchResultsDefaultLimit 	- Количество анкет на главной странице
sortingWeights[]			- Значения веса для каждого из параметров фильтрации
itemsOnUserIndexPage		- Количество анкет на странице у админа (/user/index)
itemsOnRequestIndexPage		- Количество заявок на странице у админа (/request/index)
testMail					- Адрес почты для тестирования писем, на продакшене должно быть false
testAllUsersMode			- Режим проверки всех юзеров (вне зависимости от статуса заявки и видимости), на продакшене должно быть false
mailToManagers				- Адрес почты менеджеров
mailFrom					- Адрес отправителя писем
messageSubjects[]			- Темы для отправляемых писем
messages[]					- Сообщения на сайте
statusMessages[]			- Сообщения, характеризующие текущий статус анкеты
junctionTablesSetup[]		- Настройка смежных таблиц
status[]					- Числовые значения для статусов
requestStatus[]				- Статусы заявок
report[]					- Настройка начального состояния фильтров на странице отчетов:
								defaultReportRange - начальное состояние количества дней, возможные значения: '7days', '30days', '90days', '365days', 'custom';
								defaultDetailLevel - начальный уровень детализации, возможные значения: 'days', 'weeks', 'months';
								customDateStart - (когда используется defaultReportRange => 'custom') начальная дата интервала;
								customDateEnd - (когда используется defaultReportRange => 'custom') конечная дата интервала;
contentType[]				- Числовые значения для типов материалов


-----------------------------

Для применения всех миграций необходимо обновить файл config/params.php

ВАЖНО!!! Для корректной работы системы необходимо добавить файлы из папки cron в cron на сервере и настроить в них корректные домены.

1) close-old-orders: Закрывает все заказы у которых время закрытия <= текущему
2) new-order: Рассылает письма сметчикам о новых заказах, удовлетворяющих условию соответсвия профобласти [между заказом и сметчиком]
3) send-mail-to-customer-before-close-order: Рассылает всем заказчикам уведомление, что их заказ закроется через $days дней ($days - dayToSendMailAboutFinishOrder из params.php)
4) send-remind-message-to-users: Отправка сообщений пользователям у которых кол-во дней, прошедших с момента последнего обновления, кратное числу, указанному в params.php как remindMessageDivisibleByDays.
5) send-remind-message-to-users: Отправка информационных сообщений о новых откликах на заказ

-----------------------------
Запуск тестов

- установить и настроить тестовый сервер
- создать базу на тестовом сервере
-- codeception автоматически будет залить базу с тестовыми данными из /tests/_data/*.sql настраивается в tests/codeception/acceptance.suite.yml
# !!! отключение отслеживания изменения git
# git update-index --assume-unchanged tests/codeception/acceptance.suite.yml
- на сервере в web/index-test.php добавить ip-машины на которой запущен selenium
- в файле tests/codeception/acceptance.suite.yml настроить
            url: http://smet4ik.test - домен сервера где запущен тестовый сервер
            host: 192.168.11.81 - машина на которой запущен селениум
- установить composer - см. выше
- запускаем selenium - напр java -Dwebdriver.chrome.driver=./chromedriver -jar selenium-server-standalone-2.53.0.jar
- копируем конфиги в config из config/origin
- настраиваем на тестовом сервере в tests/codeception/config/config.php имя базы
- запуск тестов - codecept run acceptance
- для запуска тестов по отчетам настроить в локальном params.php подключение к базе сервера
