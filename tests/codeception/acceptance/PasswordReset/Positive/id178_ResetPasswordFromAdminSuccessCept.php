<?php

// @group unparalleled
// @group id178

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;
use tests\codeception\_pages\ConfirmNewPasswordPage;
use tests\codeception\_controllers\AdminControllers;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю сброс пароля админом - успешный');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='admin';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->FastCheckPage();

$ID=1;

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);

$Email='mjiquy_1991@xaker.ru';
$I->amGoingTo('Отправляю письмо для сброса пароля пользователю'); // ------------------------------------------------
AdminSmet4ikEditPage::of($I)->PasswordReset($Email);

$I->amGoingTo('Выхожу из панели управления'); // ------------------------------------------------
AdminControllers::of($I)->Logout();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

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
