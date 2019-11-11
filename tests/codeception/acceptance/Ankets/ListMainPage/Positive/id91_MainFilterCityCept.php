<?php

// @group parallel
// @group id91

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка фильтр город');

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
$City='Майский (Кабардино-Балкария)';
MainPage::of($I)->ApplyCityFilter($City);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$PositionAfter=[1,2,3,4];
$DataAfter=[['Ярыгин Виктор Мирославович','Майский (Кабардино-Балкария)'],['Яльцев Егор Климентович'],['Скуратова Александра Алексеевна'],['Касьяненко Дмитрий Всеволодович']];

for($i=0;$i<4;$i++) {
    $I->amGoingTo('Проверяю человека ' . $PositionAfter[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataAfter[$i],$PositionAfter[$i]);
}

$I->amGoingTo('Очищаю фильтр город'); // ------------------------------------------------
MainPage::of($I)->ClearCityFilter();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

for($i=0;$i<4;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionBefore[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataBefore[$i],$PositionBefore[$i]);
}