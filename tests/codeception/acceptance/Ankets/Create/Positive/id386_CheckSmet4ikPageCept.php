<?php

// @group parallel
// @group id386

use tests\codeception\_pages\RegisterPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю страницу сметчика на сайту');

$I->amGoingTo('Открываю страницу регистрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Проверяю меню и тексты на странице'); // ------------------------------------------------

RegisterPage::of($I)->CheckMenu();
RegisterPage::of($I)->FullCheckPage();

$I->amGoingTo('Проверяю что формы пустые'); // ------------------------------------------------

RegisterPage::of($I)->CheckDefaultFormState();