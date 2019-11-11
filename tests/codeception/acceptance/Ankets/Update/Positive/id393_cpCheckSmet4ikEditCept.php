<?php

// @group parallel
// @group id393

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю в админке значения полей в форме редактирования сметчика');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='admin';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->FastCheckPage();

$ID=1;
$Status='требует проверки';
$Visible='показать';
$FIO='Унтилова Татьяна Алексеевна';
$Phone='+7(921)925-26-94';
$Email='mjiquy_1991@xaker.ru';
$Exp='Вот видишь, отец мой, да у тебя-то, как — подавали ревизию? — Да отчего ж? — сказал Собакевич. Чичиков подошел к ручке Маниловой. — — и что, прибывши в этот город, почел за непременный долг засвидетельствовать свое почтение первым его сановникам. Вот все, что ни есть в городе, и оно держалось до тех пор, как — подавали ревизию? —.';
$City='Барыш (Ульяновская область)';
$IPAP='526044';
$Price='530839';
$Profs=['Другое','Отделочные работы','Охранно-пожарные системы','Пусконаладочные работы','Реконструкция зданий и сооружений','Ремонтные работы по текущему и кап. ремонту','Сети связи, видеонаблюдение','Фасадные работы'];
$Docs=['Форма КС-3','Экспертиза смет','Тендерная документация'];
$Bases=['ПНР','Ведомственные','Индивидуальные/фирменные','Госэталон'];

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FullCheckPage($ID);

$I->amGoingTo('Проверяю содержимое формы'); // ------------------------------------------------
AdminSmet4ikEditPage::of($I)->CheckStatus($Status,$Visible,'');
AdminSmet4ikEditPage::of($I)->CheckDefaultFormState($FIO,$Email,$Phone,$City,$Profs,$Docs,$Bases,$IPAP,$Price,$Exp);