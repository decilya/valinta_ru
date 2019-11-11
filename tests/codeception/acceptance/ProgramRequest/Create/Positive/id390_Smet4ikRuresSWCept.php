<?php

// @group parallel
// @group id390

use tests\codeception\_pages\ProgramRequestPage;
use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\RequestAgreementPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю условия предоставления ПП SW в форме регистрации в базе');

$I->amGoingTo('Открываю страницу страницу заявки без входа в ЛК'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю наличие ссылки на соглашение'); // ------------------------------------------------
$I->seeElement('//a[@target="_blank"][@href="'. RequestAgreementPage::$URL .'"]');
$I->seeLink(ProgramRequestPage::$FormRequiredRequestAgreementLinkText);

$I->amGoingTo('Открываю пользовательское соглашение'); // ------------------------------------------------
$I->amOnPage(RequestAgreementPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RequestAgreementPage::of($I)->FastCheckPage();

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

$I->amGoingTo('Открываю страницу страницу заявки после входа в ЛК'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю наличие ссылки на соглашение'); // ------------------------------------------------
$I->seeElement('//a[@target="_blank"][@href="'. RequestAgreementPage::$URL .'"]');
$I->seeLink(ProgramRequestPage::$FormRequiredRequestAgreementLinkText);

$I->amGoingTo('Открываю пользовательское соглашение'); // ------------------------------------------------
$I->amOnPage(RequestAgreementPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RequestAgreementPage::of($I)->FastCheckPage();