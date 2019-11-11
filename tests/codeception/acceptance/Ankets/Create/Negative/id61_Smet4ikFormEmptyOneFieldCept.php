<?php

// @group parallel
// @group id61

use tests\codeception\_pages\RegisterPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю регистрацию в базе в форме регистрации одно обязательное поле пустое');

$I->amGoingTo('Открываю страницу регистрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RegisterPage::of($I)->FastCheckPage();

$FIO='Test User';
$Phone='+7(999)888-77-44';
$Email='testuser@mail.ru';
$Password='P@ssw0rd';
$PasswordRepeat='P@ssw0rd';
$City='Санкт-Петербург';
$IPAP='09876543';
$Price='3000';
$Exp='10 лет';
$ProfArray=['Автомобильные дороги'];
$SmetnDocsArray=['Локальная смета'];
$BasesArray=['ТЕР-2001'];
$Agreement=true;

$I->amGoingTo('Проверяю на обязательные поля'); // ------------------------------------------------
//RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);

RegisterPage::of($I)->Register(false,$Email,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRequiredFieldWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);
$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

RegisterPage::of($I)->Register($FIO,false,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRequiredFieldWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);
$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

RegisterPage::of($I)->Register($FIO,$Email,false,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRequiredFieldWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);
$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

RegisterPage::of($I)->Register($FIO,$Email,$Password,false,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRequiredFieldWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);
$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,false,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRequiredFieldWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);
$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,$City,[],$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRequiredFieldWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);
$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterAgreementWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);



