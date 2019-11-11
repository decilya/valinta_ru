<?php

// @group parallel
// @group id53

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить вход в кабинет сметчика');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в кабинет'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1qwe2qaz';
$userID=1;
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($userID);