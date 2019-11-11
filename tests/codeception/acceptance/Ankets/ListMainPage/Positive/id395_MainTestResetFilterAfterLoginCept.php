<?php

// @group parallel
// @group id395

use tests\codeception\_pages\MainPage;
use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Тестирую меню после нажатия сбросить фильтр');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в кабинет'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1qwe2qaz';
$userID=1;
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($userID);

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю состояние без фильтра'); // ------------------------------------------------
$Position=1;
$FIO='Яльцев Егор Климентович';
MainPage::of($I)->SeeHuman([$FIO],$Position);

$I->amGoingTo('Заполняю фильтр'); // ------------------------------------------------
$Profs=['Автомобильные дороги'];
$Bases=['ТЕР-2001'];
$Docs=['Сводный сметный расчет'];
$City='Огорелыши (Карелия)';
MainPage::of($I)->ApplyProfsFilter($Profs);
MainPage::of($I)->ApplyDocsFilter($Docs);
MainPage::of($I)->ApplyBasesFilter($Bases);
MainPage::of($I)->ApplyCityFilter($City);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Проверяю состояние после фильтра'); // ------------------------------------------------
$Position=1;
$FIO='Шепелева Ариадна Родионовна';
$Percent='66%';
$City='Огорелыши (Карелия)';
MainPage::of($I)->SeeHuman([$FIO,$Percent,$City],$Position);

$I->amGoingTo('Сбрасываю фильтр'); // ------------------------------------------------
MainPage::of($I)->ClickResetFilter();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Проверяю состояние без фильтра'); // ------------------------------------------------
$Position=1;
$FIO='Яльцев Егор Климентович';
MainPage::of($I)->SeeHuman([$FIO],$Position);