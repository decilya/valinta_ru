<?php

// @group parallel
// @group id191

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить кнопку Проверить аттестат ИПАП из редактирования анкеты');

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

$ID=60;
$IPAP='017832';

$I->amGoingTo('Захожу в редактирование анкеты и проверяю ссылку проверки аттестата'); // ------------------------------------------------
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю ссылку'); // ------------------------------------------------
$I->seeLink(AdminSmet4ikEditPage::$CheckIPAPLink);
$I->seeElement('//a[@target="_blank"][@href="http://ipap.ru/vydavaemye-dokumenty?attId='. $IPAP .'#registrySearchForm"]');