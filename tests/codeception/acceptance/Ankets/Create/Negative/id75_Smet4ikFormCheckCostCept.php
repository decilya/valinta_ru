<?php

// @group parallel
// @group id75

use tests\codeception\_pages\RegisterPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю стоимость в форме регистрации в базе');

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
$Exp='10 лет';
$ProfArray=['Автомобильные дороги'];
$BasesArray=['ТЕР-2001'];
$SmetnDocsArray=['Локальная смета'];
$Agreement=true;

$I->amGoingTo('Проверяю валидацию'); // ------------------------------------------------

$Price='10000000';
RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
//$smet4ikControllers->FillRegisterForm([RegisterPage::$FormRegisterField_FIO=>$FIO,RegisterPage::$FormRegisterField_Email=>$Email,RegisterPage::$FormRegisterField_Pass=>$Password,RegisterPage::$FormRegisterField_RepPass=>$PasswordRepeat,RegisterPage::$FormRegisterField_Phone=>$Phone,RegisterPage::$FormRegisterField_City=>$City,RegisterPage::$FormRegisterField_IPAP=>$IPAP,RegisterPage::$FormRegisterField_Prof=>$ProfArray,RegisterPage::$FormRegisterField_Docs=>$SmetnDocsArray,RegisterPage::$FormRegisterField_Bases=>$BasesArray,RegisterPage::$FormRegisterField_Exp=>$Exp,RegisterPage::$FormRegisterField_Price=>$Price,RegisterPage::$FormRegisterField_Agreement_id=>$Agreement]);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPriceWrongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

$Price='1 000 000';
RegisterPage::of($I)->Register(false,false,false,false,false,false,[],[],[],false,$Price,false,false);
//$smet4ikControllers->FillRegisterForm([RegisterPage::$FormRegisterField_Price=>$Price]);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPriceNoDigWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

$Price='-900000';
//$smet4ikControllers->FillRegisterForm([RegisterPage::$FormRegisterField_Price=>$Price]);
RegisterPage::of($I)->Register(false,false,false,false,false,false,[],[],[],false,$Price,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPriceWrongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);

$Price='5o000';
RegisterPage::of($I)->Register(false,false,false,false,false,false,[],[],[],false,$Price,false,false);
//$smet4ikControllers->FillRegisterForm([RegisterPage::$FormRegisterField_Price=>$Price]);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPriceNoDigWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);