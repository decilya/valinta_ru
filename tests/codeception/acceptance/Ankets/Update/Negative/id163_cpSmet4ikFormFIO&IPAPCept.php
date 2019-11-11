<?php

// @group parallel
// @group id163

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из системы управления - неверные ФИО и № аттестата ИПАП.');

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

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$ID=1;
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------

$FIO='Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество';
$IPAP='№ аттестата 1234';
AdminSmet4ikEditPage::of($I)->Update($FIO,false,false,false,[],[],[],$IPAP,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormFIOLongWarn);
$I->see(AdminSmet4ikEditPage::$FormIPAPLongWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);
