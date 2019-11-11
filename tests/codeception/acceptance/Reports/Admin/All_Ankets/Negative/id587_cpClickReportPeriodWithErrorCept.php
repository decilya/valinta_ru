<?php

// @group unparalleled
// @group id587

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminClickReportPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить детализацию при ручном выборе периода на странице отчетов');

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

$I->amGoingTo('Проверяю работу периода ОшИбКа - ОшИбКа дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y');
$DateTo=date('d.m.Y');
AdminClickReportPage::of($I)->SetManualPeriod('ОшИбКа','ОшИбКа');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(false,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);
AdminClickReportPage::of($I)->CheckPeriodsINSelect([AdminClickReportPage::$DetailDaysSelect],[AdminClickReportPage::$DetailWeeksSelect,AdminClickReportPage::$DetailMonthsSelect]);

$I->amGoingTo('Проверяю работу периода -0 -0 дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y');
$DateTo=date('d.m.Y');
AdminClickReportPage::of($I)->SetManualPeriod($DateFrom,$DateTo);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(false,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);
AdminClickReportPage::of($I)->CheckPeriodsINSelect([AdminClickReportPage::$DetailDaysSelect],[AdminClickReportPage::$DetailWeeksSelect,AdminClickReportPage::$DetailMonthsSelect]);