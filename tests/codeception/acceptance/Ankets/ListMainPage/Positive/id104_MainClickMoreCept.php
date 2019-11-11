<?php

// @group parallel
// @group id104

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка "Загрузить еще"');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю человека 1'); // ------------------------------------------------
$Position=1;
$FIO='Яльцев Егор Климентович';
MainPage::of($I)->SeeHuman([$FIO],$Position);

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

$I->amGoingTo('Проверяю последнего человека'); // ------------------------------------------------
$Position=19;
$FIO='Шепелева Ариадна Родионовна';
MainPage::of($I)->SeeHuman([$FIO],$Position);
