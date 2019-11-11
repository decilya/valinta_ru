<?php

// @group unparalleled
// @group id590

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

$I->amGoingTo('Проверяю работу периода -5 -45 дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y',strtotime('-45 days',time()));
$DateTo=date('d.m.Y',strtotime('-5 days',time()));
AdminClickReportPage::of($I)->SetManualPeriod($DateFrom,$DateTo);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(false,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);
AdminClickReportPage::of($I)->CheckPeriodsINSelect([AdminClickReportPage::$DetailDaysSelect,AdminClickReportPage::$DetailWeeksSelect],[AdminClickReportPage::$DetailMonthsSelect]);
AdminClickReportPage::of($I)->SelectWeeks();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(false,AdminClickReportPage::$DetailWeeksSelect,$DateFrom,$DateTo);

$I->amGoingTo('Проверяю работу периода -45 -145 дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y',strtotime('-145 days',time()));
$DateTo=date('d.m.Y',strtotime('-45 days',time()));
AdminClickReportPage::of($I)->SetManualPeriod($DateFrom,$DateTo);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(false,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);
AdminClickReportPage::of($I)->CheckPeriodsINSelect([AdminClickReportPage::$DetailDaysSelect,AdminClickReportPage::$DetailWeeksSelect,AdminClickReportPage::$DetailMonthsSelect],[]);
AdminClickReportPage::of($I)->SelectWeeks();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(false,AdminClickReportPage::$DetailWeeksSelect,$DateFrom,$DateTo);
AdminClickReportPage::of($I)->SelectMonths();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState('',AdminClickReportPage::$DetailMonthsSelect,$DateFrom,$DateTo);

$I->amGoingTo('Проверяю работу периода -20 -15 дней'); // ------------------------------------------------
$DateFrom=date('d.m.Y',strtotime('-20 days',time()));
$DateTo=date('d.m.Y',strtotime('-15 days',time()));
AdminClickReportPage::of($I)->SetManualPeriod($DateFrom,$DateTo);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminClickReportPage::of($I)->CheckPageState(false,AdminClickReportPage::$DetailDaysSelect,$DateFrom,$DateTo);
AdminClickReportPage::of($I)->CheckPeriodsINSelect([AdminClickReportPage::$DetailDaysSelect],[AdminClickReportPage::$DetailWeeksSelect,AdminClickReportPage::$DetailMonthsSelect]);