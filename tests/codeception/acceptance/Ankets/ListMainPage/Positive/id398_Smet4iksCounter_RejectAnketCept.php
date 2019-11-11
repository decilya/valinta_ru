<?php

// @group unparalleled
// @group id398

use tests\codeception\_pages\MainPage;
use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю счётчик опубликованных анкет - после отклонения анкеты');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();
$I->see(MainPage::$CounterText.'19');

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

$ID=33;
$Status='подтверждена';
$FIO='Пьяныха Светлана Антониновна';
$Reason='Проверяем счётчик опубликованных анкет.';

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$FIO],$ID);

$I->amGoingTo('Отклоняю анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->Reject($ID,$Reason,true);

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();
$I->see(MainPage::$CounterText.'18');