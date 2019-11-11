<?php

// @group parallel
// @group id77

use tests\codeception\_pages\RegisterPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю ФИО и № аттестата ИПАП в форме регистрации в базе');

$I->amGoingTo('Открываю страницу регистрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RegisterPage::of($I)->FastCheckPage();

$FIO='Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество Фамилия Имя Отчество';
$Phone='+7(999)888-77-44';
$Email='testuser@mail.ru';
$Password='P@ssw0rd';
$PasswordRepeat='P@ssw0rd';
$City='Санкт-Петербург';
$IPAP='№ аттестата 1234';
$Price='3000';
$Exp='10 лет';
$ProfArray=['Автомобильные дороги'];
$BasesArray=['ТЕР-2001'];
$SmetnDocsArray=['Локальная смета'];
$Agreement=true;

$I->amGoingTo('Проверяю валидацию'); // ------------------------------------------------
RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,$City,$ProfArray,$SmetnDocsArray,$BasesArray,$IPAP,$Price,$Exp,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRegisterFIOLongWarn);
$I->see(RegisterPage::$FormRegisterIPAPLongWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);