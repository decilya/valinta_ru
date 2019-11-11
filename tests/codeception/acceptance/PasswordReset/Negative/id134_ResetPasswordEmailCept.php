<?php

// @group parallel
// @group id134

use tests\codeception\_pages\RecoverPasswordPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю сброс пароля - проверка e-mail');

$I->amGoingTo('Проверяю форму запроса восстановления пароля'); // ------------------------------------------------
$I->amOnPage(RecoverPasswordPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RecoverPasswordPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю пустую форму запроса восстановления пароля'); // ------------------------------------------------
$username='';
RecoverPasswordPage::of($I)->SubmitForPassword($username);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RecoverPasswordPage::$EmptyFieldWarning);
$I->dontSee(RecoverPasswordPage::$SuccessWarning);

$I->amGoingTo('Проверяю несуществующий email в форме запроса восстановления пароля'); // ------------------------------------------------
$username='testmail@test.mail';
RecoverPasswordPage::of($I)->SubmitForPassword($username);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RecoverPasswordPage::$WrongDataWarning);
$I->dontSee(RecoverPasswordPage::$SuccessWarning);