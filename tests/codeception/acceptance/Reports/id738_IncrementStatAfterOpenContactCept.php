<?php

// @group unparalleled
// @group id738

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю статистику просмотром в личном кабинете');

$I->amGoingTo('Генерирую данные для отчетов'); // ------------------------------------------------
exec('php ../yii app/create-report-all');

$ID=36;

$I->amGoingTo('Собираю статистику из базы'); // ------------------------------------------------
$CurDate=date('d.m.Y',time());
$_30daysBefore=0;
for($i=0;$i<30;$i++){
    $Date=date('Y-m-d',strtotime('-'.$i.' days',time($CurDate)));
    $_30daysBefore=$_30daysBefore+$I->grabNumRecords('reports',['user_id' => $ID,'date like' => $Date.'%']);
}
$AllTimeBefore=$I->grabNumRecords('reports',['user_id' => $ID]);

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Открываю контакты'); // ------------------------------------------------
$Position=1;
$FIO='Яльцев Егор Климентович';
$Phone='+7(940)269-69-51';
$Email='njaschyech@nextmail.ru';
MainPage::of($I)->SeeHuman([$FIO],$Position);
MainPage::of($I)->SeeContact($Phone,$Email,$Position);

$I->amGoingTo('Собираю статистику из базы'); // ------------------------------------------------
$_30daysAfter=0;
for($i=0;$i<30;$i++){
    $Date=date('Y-m-d',strtotime('-'.$i.' days',time($CurDate)));
    $_30daysAfter=$_30daysAfter+$I->grabNumRecords('reports',['user_id' => $ID,'date like' => $Date.'%']);
}
$AllTimeAfter=$I->grabNumRecords('reports',['user_id' => $ID]);

if ($_30daysBefore+1 !== $_30daysAfter) $I->see('Ошибка в подсчетах 30 дней');
if ($AllTimeBefore+1 !== $AllTimeAfter) $I->see('Ошибка в подсчетах всего периода');