<?php

// @group parallel
// @group id117

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminRequestListPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить сортировку запросов программы по умолчанию в панели управления');

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

$I->amGoingTo('Перехожу на страницу заявок'); // ------------------------------------------------
$I->amOnPage(AdminRequestListPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю сортировку по датам + времени'); // ------------------------------------------------
$Dates=AdminRequestListPage::of($I)->GrabCreateTime();
for ($i=0; $i<count($Dates)-1;$i++) {
    if (strtotime($Dates[$i])<strtotime($Dates[$i+1])) $I->see('Вижу что сортировка по времени не верно в строке '.$i.': "'.$Dates[$i].'" < "'.$Dates[$i+1].'"');
}