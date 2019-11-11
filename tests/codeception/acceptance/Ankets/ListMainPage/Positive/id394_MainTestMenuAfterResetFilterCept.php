<?php

// @group parallel
// @group id394

use tests\codeception\_pages\MainPage;
use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Тестирую меню после нажатия сбросить фильтр');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю меню на странице'); // ------------------------------------------------
MainPage::of($I)->CheckMenuSite();

$I->amGoingTo('Проверяю меню на странице после сброса фильтра'); // ------------------------------------------------
MainPage::of($I)->ClickResetFilter();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->CheckMenuSite();

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

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю меню на странице'); // ------------------------------------------------
MainPage::of($I)->CheckMenuLK();

$I->amGoingTo('Проверяю меню на странице после сброса фильтра'); // ------------------------------------------------
MainPage::of($I)->ClickResetFilter();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->CheckMenuLK();
