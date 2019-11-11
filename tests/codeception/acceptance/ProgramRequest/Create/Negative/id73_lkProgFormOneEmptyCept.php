<?php

// @group parallel
// @group id73

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\ProgramRequestPage;
use tests\codeception\_pages\lkSmet4ikPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку формы заявки на программу с одним пустым полем из личного кабинета');

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

$I->amGoingTo('Открываю страницу заявки на программу'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------
$FIO='';
$Agreement=true;
ProgramRequestPage::of($I)->SendRequest($FIO,false,false,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormRequiredFieldWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
$I->reloadPage();
//if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$Email='';
$Agreement=true;
ProgramRequestPage::of($I)->SendRequest(false,$Email,false,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormRequiredFieldWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
$I->reloadPage();
//if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$Phone='';
$Agreement=true;
ProgramRequestPage::of($I)->SendRequest(false,false,$Phone,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormRequiredFieldWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
$I->reloadPage();
//if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$Agreement=false;
ProgramRequestPage::of($I)->SendRequest(false,false,false,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramAgreementWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
