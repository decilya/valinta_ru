<?php

// @group unparalleled
// @group id549

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminClickReportPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить работу кнопок периодов на странице отчетов');

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

$DateTo=date('d.m.Y',time());

$I->amGoingTo('Проверяю работу 30 дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y',strtotime('-29 days',time($DateTo)));
AdminClickReportPage::of($I)->Click30Days();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(AdminClickReportPage::$_30daysIDButton,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);

$I->amGoingTo('Проверяю работу 90 дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y',strtotime('-89 days',time($DateTo)));
AdminClickReportPage::of($I)->Click90Days();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(AdminClickReportPage::$_90daysIDButton,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);

$I->amGoingTo('Проверяю работу 365 дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y',strtotime('-364 days',time($DateTo)));
AdminClickReportPage::of($I)->Click365Days();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(AdminClickReportPage::$_365daysIDButton,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);

$I->amGoingTo('Проверяю работу 7 дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y',strtotime('-6 days',time($DateTo)));
AdminClickReportPage::of($I)->Click7Days();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(AdminClickReportPage::$_7daysIDButton,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);