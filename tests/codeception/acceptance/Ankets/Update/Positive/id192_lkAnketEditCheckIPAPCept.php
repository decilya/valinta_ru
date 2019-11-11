<?php

// @group parallel
// @group id192

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить кнопку Проверить аттестат ИПАП из личного кабинета');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в кабинет сметчика'); // ------------------------------------------------
$username='gefonyu@riociulij.net';
$password='1qwe2qaz';
$userID=60;
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($userID);

$IPAP='017832';

$I->amGoingTo('Проверяю ссылку'); // ------------------------------------------------
$I->seeLink(lkSmet4ikPage::$CheckIPAPLink);
$I->seeElement('//a[@target="_blank"][@href="http://ipap.ru/vydavaemye-dokumenty?attId='. $IPAP .'#registrySearchForm"]');
