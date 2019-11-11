<?php

// @group parallel
// @group id103

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка "Очистить фильтр"');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$PositionBefore=[1,2,3];
$DataBefore=[['Яльцев Егор Климентович'],['Скуратова Александра Алексеевна'],['Касьяненко Дмитрий Всеволодович']];

for($i=0;$i<3;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionBefore[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataBefore[$i],$PositionBefore[$i]);
}

$I->amGoingTo('Заполняю фильтр'); // ------------------------------------------------
$Profs=['Автомобильные дороги'];
$Bases=['ТЕР-2001'];
$Docs=['Сводный сметный расчет'];
$City='Огорелыши (Карелия)';
MainPage::of($I)->ApplyProfsFilter($Profs);
MainPage::of($I)->ApplyDocsFilter($Docs);
MainPage::of($I)->ApplyBasesFilter($Bases);
MainPage::of($I)->ApplyCityFilter($City);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Проверяю состояние после фильтра'); // ------------------------------------------------
$Position=1;
$FIO='Шепелева Ариадна Родионовна';
$Percent='66%';
$City='Огорелыши (Карелия)';
MainPage::of($I)->SeeHuman([$FIO,$Percent,$City],$Position);

$I->amGoingTo('Сбрасываю фильтр'); // ------------------------------------------------
MainPage::of($I)->ClickResetFilter();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Проверяю состояние после сброса'); // ------------------------------------------------
$PositionBefore=[1,2,3];
$DataBefore=[['Яльцев Егор Климентович'],['Скуратова Александра Алексеевна'],['Касьяненко Дмитрий Всеволодович']];

for($i=0;$i<3;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionBefore[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataBefore[$i],$PositionBefore[$i]);
}