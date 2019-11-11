<?php

// @group unparalleled
// @group id594

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminClickReportPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить состотяние страницы отчетов по умолчанию');

$I->amGoingTo('Генерирую данные для отчетов'); // ------------------------------------------------
exec('php ../yii app/create-report-all');

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

$I->amGoingTo('Открываю страницу отчетов'); // ------------------------------------------------
$I->amOnPage(AdminClickReportPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю состояние'); // ------------------------------------------------

$DateTo=date('d.m.Y',time());
$DateFrom=date('d.m.Y',strtotime('-6 days',time($DateTo)));

AdminClickReportPage::of($I)->CheckPageState(AdminClickReportPage::$_7daysIDButton,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);