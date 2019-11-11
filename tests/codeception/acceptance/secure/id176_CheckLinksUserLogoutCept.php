<?php

// @group parallel
// @group id176

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\ConfirmNewPasswordPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить доступ к чужой анкете из своего личного кабинета');

$I->amOnPage('/user/index');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/request/index');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/user/update/1');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/user/accept-user/1');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/user/reject-user/1');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/user/change-visibility/1');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/report/all');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/report/user/3');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/user/send-instructions/1');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amOnPage('/change-pass');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ConfirmNewPasswordPage::$ErrorWarning);
