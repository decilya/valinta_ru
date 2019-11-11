<?php

// @group parallel
// @group id64

use tests\codeception\_pages\RegisterPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю мобильный телефон в форме регистрации в базе');

$I->amGoingTo('Открываю страницу регитсрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RegisterPage::of($I)->FastCheckPage();

$FIO='Test User';
$Phone='+7(8787)78-78-96';
$Email='testuser@mail.ru';
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
RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
//$smet4ikControllers->FillRegisterForm([Smet4ikPage::$FormRegisterField_FIO=>$FIO,Smet4ikPage::$FormRegisterField_Email=>$Email,Smet4ikPage::$FormRegisterField_Pass=>$Password,Smet4ikPage::$FormRegisterField_RepPass=>$PasswordRepeat,Smet4ikPage::$FormRegisterField_Phone=>$Phone,Smet4ikPage::$FormRegisterField_City=>$City,Smet4ikPage::$FormRegisterField_IPAP=>$IPAP,Smet4ikPage::$FormRegisterField_Prof=>$ProfArray,Smet4ikPage::$FormRegisterField_Docs=>$SmetnDocsArray,Smet4ikPage::$FormRegisterField_Bases=>$BasesArray,Smet4ikPage::$FormRegisterField_Exp=>$Exp,Smet4ikPage::$FormRegisterField_Price=>$Price,Smet4ikPage::$FormRegisterField_Agreement_id=>$Agreement]);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterPhoneWrongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);
