<?php

// @group parallel
// @group id71

use tests\codeception\_pages\ProgramRequestPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку формы заявки на программу с одним пустым полем');

$I->amGoingTo('Открываю страницу заявки на программу'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------

$FIO='Унтилова Татьяна Алексеевна';
$Email='mjiquy_1991@xaker.ru';
$Phone='+7(921)925-26-94';
$Agreement=true;

ProgramRequestPage::of($I)->SendRequest(false,$Email,$Phone,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormRequiredFieldWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
$I->reloadPage();

ProgramRequestPage::of($I)->SendRequest($FIO,false,$Phone,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormRequiredFieldWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
$I->reloadPage();

ProgramRequestPage::of($I)->SendRequest($FIO,$Email,false,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormRequiredFieldWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
$I->reloadPage();

ProgramRequestPage::of($I)->SendRequest($FIO,$Email,$Phone,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramAgreementWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
