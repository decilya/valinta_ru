<?php

// @group id389

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить отсутствие сметчиков на странице поиска');

$I->amGoingTo('Очищаю базу'); // ------------------------------------------------
$I->executeOnDatabase('TRUNCATE TABLE users;');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю тексты на странице'); // ------------------------------------------------
$I->see(MainPage::$NoRecordsText);
