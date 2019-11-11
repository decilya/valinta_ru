<?php

// @group parallel
// @group id89

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка фильтр нормативные базы');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$PositionBefore=[1,2,3,10];
$DataBefore=[['Яльцев Егор Климентович'],['Скуратова Александра Алексеевна'],['Касьяненко Дмитрий Всеволодович'],['Лобан Савелий Иннокентиевич']];

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

for($i=0;$i<4;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionBefore[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataBefore[$i],$PositionBefore[$i]);
}

$I->amGoingTo('Заполняю фильтр'); // ------------------------------------------------
$Bases=['Госэталон','ТСН-2001 Москва','ФЕР-2001','ПИР'];
MainPage::of($I)->ApplyBasesFilter($Bases);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$PositionAfter=[1,2,3,4];
$DataAfter=[
    ['Касьяненко Дмитрий Всеволодович','75%','ГЭСН; ТСН-2001 Москва; Госэталон; Ведомственные; ТЕР-2001; ПНР; Индивидуальные/фирменные; ФЕР-2001'],
    ['Климов Емельян Владиславович','50%','ТСНБ-2001; ФЕР-2001; Прочее; ПИР; ГЭСН'],
    ['Мананникова Виктория Родионовна','50%','Ведомственные; ТСНБ-2001; ТСН-2001 Москва; ТЕР-2001; Индивидуальные/фирменные; ПИР; Прочее'],
    ['Лобан Савелий Иннокентиевич','50%','ПНР; Индивидуальные/фирменные; ТСН-2001 Москва; Госэталон; ТЕР-2001']
];

for($i=0;$i<4;$i++) {
    $I->amGoingTo('Проверяю человека ' . $PositionAfter[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataAfter[$i],$PositionAfter[$i]);
}

$I->amGoingTo('Очищаю фильтр'); // ------------------------------------------------
MainPage::of($I)->ClearBasesFilter($Bases);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

for($i=0;$i<4;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionBefore[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataBefore[$i],$PositionBefore[$i]);
}