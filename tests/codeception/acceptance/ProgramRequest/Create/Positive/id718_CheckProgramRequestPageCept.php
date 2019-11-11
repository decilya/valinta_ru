<?php

// @group parallel
// @group id718

use tests\codeception\_pages\ProgramRequestPage;
use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю страницу заявки на программу');

$I->amGoingTo('Открываю страницу страницу заявки без входа в ЛК'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Проверяю меню и тексты на странице'); // ------------------------------------------------

ProgramRequestPage::of($I)->CheckMenuSite();
ProgramRequestPage::of($I)->FullCheckPage();

$I->amGoingTo('Проверяю что формы пустые'); // ------------------------------------------------
$FIO='';
$Email='';
$Phone='';
$Agreement=false;
ProgramRequestPage::of($I)->CheckDefaultFormState($FIO,$Email,$Phone,$Agreement);

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

$I->amGoingTo('Проверяю меню и тексты на странице'); // ------------------------------------------------

ProgramRequestPage::of($I)->CheckMenuLK();
ProgramRequestPage::of($I)->FullCheckPage();

$I->amGoingTo('Проверяю что форма заполнена'); // ------------------------------------------------
$FIO='Унтилова Татьяна Алексеевна';
$Email='mjiquy_1991@xaker.ru';
$Phone='+7(921)925-26-94';
$Agreement=false;
ProgramRequestPage::of($I)->CheckDefaultFormState($FIO,$Email,$Phone,$Agreement);