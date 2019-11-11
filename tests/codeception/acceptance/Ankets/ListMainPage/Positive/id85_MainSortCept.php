<?php

// @group parallel
// @group id85

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка фильтр сметная документация');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$PositionBefore=[1,2,3,10];
$DataBefore=[['Яльцев Егор Климентович','91506 руб.'],['Скуратова Александра Алексеевна','199329 руб.'],['Касьяненко Дмитрий Всеволодович','228617 руб.'],['Лобан Савелий Иннокентиевич','746256 руб.']];

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

for($i=0;$i<4;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionBefore[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataBefore[$i],$PositionBefore[$i]);
}

$I->amGoingTo('Меняю сортировку на - стоимость больше'); // ------------------------------------------------
MainPage::of($I)->ClickSortPriceDesc();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$PositionAfter=[1,2,3,10];
$DataAfter=[['Лобан Савелий Иннокентиевич','746256 руб.'],['Вышегородских Кир Давидович','474728 руб.'],['Пьяныха Светлана Антониновна','470603 руб.'],['Яльцев Егор Климентович','91506 руб.']];

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

for($i=0;$i<4;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionAfter[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataAfter[$i],$PositionAfter[$i]);
}

$I->amGoingTo('Меняю сортировку на - стоимость меньше'); // ------------------------------------------------
MainPage::of($I)->ClickSortPriceAsc();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

for($i=0;$i<4;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionBefore[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataBefore[$i],$PositionBefore[$i]);
}