<?php

// @group parallel
// @group id51

use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить вход в панель управления c пустыми логином и/или паролем');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в панель управления с пустым паролем'); // ------------------------------------------------
$username='';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(LoginPage::$EmptyFieldWarning);

$I->amGoingTo('Вхожу в панель управления с пустой формой'); // ------------------------------------------------
$username='';
$password='';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(LoginPage::$EmptyFieldWarning);
