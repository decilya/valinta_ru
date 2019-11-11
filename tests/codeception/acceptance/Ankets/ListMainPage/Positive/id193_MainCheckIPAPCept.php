<?php

// @group parallel
// @group id193

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить аттестат ИПАП c главной страницы ');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$Position=13;
$FIO='Бормотова Дарья Константиновна';
$IPAP='017832';

$I->amGoingTo('Проверяю человека'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();
MainPage::of($I)->SeeHuman([$FIO,$IPAP],$Position);

$I->amGoingTo('Проверяю ссылку'); // ------------------------------------------------
$I->seeElement('//div[@data-position="'.$Position.'"] //a[@target="_blank"][@href="http://ipap.ru/vydavaemye-dokumenty?attId='. $IPAP .'#registrySearchForm"]');