<?php

// @group parallel
// @group id62

use tests\codeception\_pages\RegisterPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю email в форме регистрации в базе');

$I->amGoingTo('Открываю страницу регитсрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RegisterPage::of($I)->FastCheckPage();

$FIO='Test User';
$Phone='+7(999)888-77-44';
$Password='P@ssw0rd';
$PasswordRepeat='P@ssw0rd';
$City='Санкт-Петербург';
$IPAP='09876543';
$Price='3000';
$Exp='10 лет';
$ProfArray=['Автомобильные дороги'];
$BasesArray=['ТЕР-2001'];
$SmetnDocsArray=['Локальная смета'];
$Agreement=true;

$I->amGoingTo('Проверяю валидацию'); // ------------------------------------------------

$Email='mail@mail';
RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterEmailWrongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

// «E-mail» (64@65)
$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmail@mailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
RegisterPage::of($I)->Register(false,$Email,false,false,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (64@65)');
$I->see(RegisterPage::$FormRegisterEmailLongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

// «E-mail» (65@64)
$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmailm@ailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
RegisterPage::of($I)->Register(false,$Email,false,false,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (65@64)');
$I->see(RegisterPage::$FormRegisterEmailLongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

// «E-mail» (65@63)
$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmailm@ilmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
RegisterPage::of($I)->Register(false,$Email,false,false,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (65@63)');
$I->see(RegisterPage::$FormRegisterEmailWrongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

// «E-mail» (63@65)
$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmai@mailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
RegisterPage::of($I)->Register(false,$Email,false,false,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (63@65)');
$I->see(RegisterPage::$FormRegisterEmailWrongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

//кирилица до @
$Email='моя_почта@mail.mail';
RegisterPage::of($I)->Register(false,$Email,false,false,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» кирилица до @');
$I->see(RegisterPage::$FormRegisterEmailWrongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

//уже зарегистрирован
$Email='mjiquy_1991@xaker.ru';
RegisterPage::of($I)->Register(false,$Email,false,false,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» уже зарегистрирован');
$I->see(RegisterPage::$FormRegisterEmailExistWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);