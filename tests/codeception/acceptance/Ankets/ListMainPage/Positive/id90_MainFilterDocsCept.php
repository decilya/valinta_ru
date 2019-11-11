<?php

// @group parallel
// @group id90

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка фильтр сметная документация');

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
$Docs=['Локальная смета','Сводный сметный расчет/Объектная смета','Форма КС-6а','Экспертиза смет','Тендерная документация'];
MainPage::of($I)->ApplyDocsFilter($Docs);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$PositionAfter=[1,2,3,4];
$DataAfter=[
    ['Вышегородских Кир Давидович','60%','Форма КС-6а; Акт КС-2; Тендерная документация; Локальная смета'],
    ['Дорофеева Наталья Игнатиевна','60%','Форма КС-3; Тендерная документация; Сводный сметный расчет/Объектная смета; Экспертиза смет'],
    ['Скуратова Александра Алексеевна','40%','Экспертиза смет; Сводный сметный расчет/Объектная смета; Акт КС-2'],
    ['Мананникова Виктория Родионовна','40%','Форма КС-3; Тендерная документация; Локальная смета']
];

for($i=0;$i<4;$i++) {
    $I->amGoingTo('Проверяю человека ' . $PositionAfter[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataAfter[$i],$PositionAfter[$i]);
}

$I->amGoingTo('Очищаю фильтр'); // ------------------------------------------------
MainPage::of($I)->ClearDocsFilter($Docs);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

for($i=0;$i<4;$i++){
    $I->amGoingTo('Проверяю человека '. $PositionBefore[$i]); // ------------------------------------------------
    MainPage::of($I)->SeeHuman($DataBefore[$i],$PositionBefore[$i]);
}