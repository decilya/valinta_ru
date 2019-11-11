<?php

// @group parallel
// @group id70

use tests\codeception\_pages\ProgramRequestPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку формы заявки на программу с неправильным e-mail');

$I->amGoingTo('Открываю страницу заявки на программу'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------

$FIO='Test User';
$Email='mail@mail';
$Phone='7(999)888-77-44';
$Agreement=true;

ProgramRequestPage::of($I)->SendRequest($FIO,$Email,$Phone,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramEmailWrongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmail@mailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';

ProgramRequestPage::of($I)->SendRequest(false,$Email,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('проверяю «E-mail» (64@65)');
$I->see(ProgramRequestPage::$FormGetProgramEmailLongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmailm@ailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';

ProgramRequestPage::of($I)->SendRequest(false,$Email,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('проверяю «E-mail» (64@65)');
$I->see(ProgramRequestPage::$FormGetProgramEmailLongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmailm@ilmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';

ProgramRequestPage::of($I)->SendRequest(false,$Email,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('проверяю «E-mail» (65@63)');
$I->see(ProgramRequestPage::$FormGetProgramEmailWrongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmai@mailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';

ProgramRequestPage::of($I)->SendRequest(false,$Email,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('проверяю «E-mail» (63@65)');
$I->see(ProgramRequestPage::$FormGetProgramEmailWrongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);

$Email='моя_почта@mail.mail';

ProgramRequestPage::of($I)->SendRequest(false,$Email,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramEmailWrongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
