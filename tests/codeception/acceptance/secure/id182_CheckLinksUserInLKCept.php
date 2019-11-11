<?php

// @group parallel
// @group id182

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\ConfirmNewPasswordPage;
use tests\codeception\_pages\Err403Page;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить доступ к чужой анкете из своего личного кабинета');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в кабинет сметчика'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1qwe2qaz';
$userID=1;
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($userID);

$I->amOnPage('/user/update/3');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/user/change-visibility/3');
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/user/accept-user/3');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/user/reject-user/3');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/user/send-instructions/3');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/change-pass');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$ErrorWarning);

$I->amOnPage('/login');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($userID);

$I->amOnPage('/user/index');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/request/index');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/request/index');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/report/all');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/report/user/3 ');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();

$I->amOnPage('/report/user/1');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
Err403Page::of($I)->FastCheckPage();