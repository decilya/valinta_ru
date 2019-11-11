<?php

// @group parallel
// @group id166

use tests\codeception\_pages\RecoverPasswordPage;
use tests\codeception\_pages\ConfirmNewPasswordPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю сброс пароля - проверка нового пароля');

$I->amGoingTo('Проверяю форму запроса восстановления пароля'); // ------------------------------------------------
$I->amOnPage(RecoverPasswordPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RecoverPasswordPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю существующий email в форме запроса восстановления пароля'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
RecoverPasswordPage::of($I)->SubmitForPassword($username);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RecoverPasswordPage::$SuccessWarning);

$I->amGoingTo('Перехожу на форму смены пароля'); // ------------------------------------------------

$RecoveryToken = $I->grabFromDatabase('auth','recovery_token',array('login' => 'mjiquy_1991@xaker.ru'));
$I->amOnPage(ConfirmNewPasswordPage::$URL.$RecoveryToken);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ConfirmNewPasswordPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю наличие подсказок к полям'); // ------------------------------------------------
$I->see(ConfirmNewPasswordPage::$HelpText);

$I->amGoingTo('Проверяю форму смены пароля на формат пароля'); // ------------------------------------------------
$password='';
$passwordrepeat='';
ConfirmNewPasswordPage::of($I)->SetNewPassword($password,$passwordrepeat);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$EmptyFieldWarning);
$I->dontSee(ConfirmNewPasswordPage::$SuccessWarning);

$password='P@ssword';
$passwordrepeat='';
ConfirmNewPasswordPage::of($I)->SetNewPassword($password,$passwordrepeat);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$DigitWarning);
$I->dontSee(ConfirmNewPasswordPage::$SuccessWarning);

$password='p@ssw0rd';
$passwordrepeat='';
ConfirmNewPasswordPage::of($I)->SetNewPassword($password,$passwordrepeat);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$UpLetterWarning);
$I->dontSee(ConfirmNewPasswordPage::$SuccessWarning);

$password='Pw0rd';
$passwordrepeat='';
ConfirmNewPasswordPage::of($I)->SetNewPassword($password,$passwordrepeat);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$ShortPasswordWarning);
$I->dontSee(ConfirmNewPasswordPage::$SuccessWarning);

$password='P@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rd';
$passwordrepeat='';
ConfirmNewPasswordPage::of($I)->SetNewPassword($password,$passwordrepeat);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$LongPasswordWarning);
$I->dontSee(ConfirmNewPasswordPage::$SuccessWarning);

$password='P@ssw0rd';
$passwordrepeat='password';
ConfirmNewPasswordPage::of($I)->SetNewPassword($password,$passwordrepeat);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$RepeatWarning);
$I->dontSee(ConfirmNewPasswordPage::$SuccessWarning);
