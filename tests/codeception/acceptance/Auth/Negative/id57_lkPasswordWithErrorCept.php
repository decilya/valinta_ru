<?php

// @group parallel
// @group id57

use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить возможность входа в кабинет сметчика с ошибочным паролем');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в кабинет сметчика'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1йцу2йфя';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(LoginPage::$WrongDataWarning);

$I->amGoingTo('Вхожу в кабинет сметчика с пустым паролем'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(LoginPage::$EmptyFieldWarning);