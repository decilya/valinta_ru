<?php

// @group parallel
// @group id719

use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю страницу сметчика на сайту');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Проверяю меню и тексты на странице'); // ------------------------------------------------

LoginPage::of($I)->CheckMenu();
LoginPage::of($I)->FullCheckPage();

$I->amGoingTo('Проверяю что формы пустые'); // ------------------------------------------------
LoginPage::of($I)->CheckDefaultFormState();