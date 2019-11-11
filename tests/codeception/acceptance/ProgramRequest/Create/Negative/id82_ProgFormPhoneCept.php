<?php

// @group parallel
// @group id82

use tests\codeception\_pages\ProgramRequestPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку формы заявки на программу с неправильным телефоном');

$I->amGoingTo('Открываю страницу заявки на программу'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------

$FIO='Test User';
$Email='mail@mail.mail';
$Phone='+7(8787)78-78-96';
$Agreement=true;
ProgramRequestPage::of($I)->SendRequest($FIO,$Email,$Phone,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramPhoneWrongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);

