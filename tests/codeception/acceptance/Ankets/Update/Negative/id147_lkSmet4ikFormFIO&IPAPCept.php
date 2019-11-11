<?php

// @group parallel
// @group id147

use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из личного кабинета - неверные ФИО и № аттестата ИПАП.');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$ID=1;

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------

$FIO='Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество';
$IPAP='№ аттестата 1234';
lkSmet4ikPage::of($I)->Update($FIO,false,false,false,[],[],[],$IPAP,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormFIOLongWarn);
$I->see(lkSmet4ikPage::$FormIPAPLongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);
