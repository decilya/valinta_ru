<?php

// @group parallel
// @group id74

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\ProgramRequestPage;
use tests\codeception\_pages\lkSmet4ikPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку формы заявки на программу с неверным e-mail из личного кабинета');

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

$Email='mail@mail';
$Agreement=true;

ProgramRequestPage::of($I)->SendRequest(false,$Email,false,$Agreement);
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

