<?php

// @group parallel
// @group id63

use tests\codeception\_pages\RegisterPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю пароль в форме регистрации в базе');

$I->amGoingTo('Открываю страницу регитсрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RegisterPage::of($I)->FastCheckPage();

$FIO='Test User';
$Phone='+7(999)888-77-44';
$Email='testuser@mail.ru';
$City='Санкт-Петербург';
$IPAP='09876543';
$Price='3000';
$Exp='10 лет';
$ProfArray=['Автомобильные дороги'];
$BasesArray=['ТЕР-2001'];
$SmetnDocsArray=['Локальная смета'];
$Agreement=true;

$I->amGoingTo('Проверяю валидацию'); // ------------------------------------------------

$Password='P@ssword';
$PasswordRepeat='';
RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPassDigWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

$Password='p@ssw0rd';
$PasswordRepeat='';
RegisterPage::of($I)->Register(false,false,$Password,$PasswordRepeat,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPassCapsWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

$Password='Pw0rd';
$PasswordRepeat='';
RegisterPage::of($I)->Register(false,false,$Password,$PasswordRepeat,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPassShortWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

$Password='P@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rdP@ssw0rd';
$PasswordRepeat='';
RegisterPage::of($I)->Register(false,false,$Password,$PasswordRepeat,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPassLongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

$Password='P@ssw0rd';
$PasswordRepeat='password';
RegisterPage::of($I)->Register(false,false,$Password,$PasswordRepeat,false,false,[],[],[],false,false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPassRepeatWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);