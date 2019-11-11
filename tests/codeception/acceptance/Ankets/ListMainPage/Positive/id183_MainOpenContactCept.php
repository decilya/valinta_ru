<?php

// @group parallel
// @group id183

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка открытия контакта сметчика');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю человека 1'); // ------------------------------------------------
$Position=1;
$FIO='Яльцев Егор Климентович';
$Phone='+7(940)269-69-51';
$Email='njaschyech@nextmail.ru';
MainPage::of($I)->SeeHuman([$FIO],$Position);
MainPage::of($I)->SeeContact($Phone,$Email,$Position);
