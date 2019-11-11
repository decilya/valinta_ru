<?php

// @group unparalleled
// @group id132

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\RecoverPasswordPage;
use tests\codeception\_pages\ConfirmNewPasswordPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю сброс пароля - успешный');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю форму запроса восстановления пароля'); // ------------------------------------------------

LoginPage::of($I)->PasswordReset();
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

$I->amGoingTo('Меняю пароль'); // ------------------------------------------------
$password='P@ssw0rd';
$passwordrepeat='P@ssw0rd';
ConfirmNewPasswordPage::of($I)->SetNewPassword($password,$passwordrepeat);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$SuccessWarning);

$I->amGoingTo('Закрываю сообщение о смене пароля'); // ------------------------------------------------
ConfirmNewPasswordPage::of($I)->CloseSuccessMessage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю новый пароль на работоспособность'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='P@ssw0rd';
$userID=1;
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($userID);

$I->amGoingTo('Перехожу повторно на форму смены пароля'); // ------------------------------------------------
$I->amOnPage(ConfirmNewPasswordPage::$URL.$RecoveryToken);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->see('Произошла ошибка при смене пароля.');
$I->see(ConfirmNewPasswordPage::$ErrorWarning);
