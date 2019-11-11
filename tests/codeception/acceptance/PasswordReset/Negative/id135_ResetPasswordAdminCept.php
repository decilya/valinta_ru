<?php

// @group parallel
// @group id135

use tests\codeception\_pages\RecoverPasswordPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю сброс пароля - admin');

$I->amGoingTo('Проверяю форму запроса восстановления пароля'); // ------------------------------------------------
$I->amOnPage(RecoverPasswordPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RecoverPasswordPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю запроса восстановления пароля для admin'); // ------------------------------------------------
$username='admin';
RecoverPasswordPage::of($I)->SubmitForPassword($username);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RecoverPasswordPage::$WrongDataWarning);
$I->dontSee(RecoverPasswordPage::$SuccessWarning);