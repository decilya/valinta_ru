<?php

// @group unparalleled
// @group id589

use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю статистику просмотром в личном кабинете');

$I->amGoingTo('Генерирую данные для отчетов'); // ------------------------------------------------
exec('php ../yii app/create-report-all');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$ID=36;

$I->amGoingTo('Вхожу в личный кабинет'); // ------------------------------------------------
$username='njaschyech@nextmail.ru';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Собираю статистику из базы'); // ------------------------------------------------
$CurDate=date('d.m.Y',time());
$_30days=0;
for($i=0;$i<30;$i++){
    $Date=date('Y-m-d',strtotime('-'.$i.' days',time($CurDate)));
    $_30days=$_30days+$I->grabNumRecords('reports',['user_id' => $ID,'date like' => $Date.'%']);
}
$AllTime=$I->grabNumRecords('reports',['user_id' => $ID]);

$I->amGoingTo('Проверяю блок статистики'); // ------------------------------------------------
lkSmet4ikPage::of($I)->CheckOpenContactsStat($_30days,$AllTime);
