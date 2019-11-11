<?php

// @group unparalleled
// @group id52

use tests\codeception\_pages\RegisterPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю регистрацию в базе в форме регистрации заполнены только обязательные поля');

$I->amGoingTo('Открываю страницу регистрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RegisterPage::of($I)->FastCheckPage();

$I->amGoingTo('Регестрируюсь'); // ------------------------------------------------

$FIO='Test User';
$Phone='+7(999)888-77-44';
$Email='testuser@mail.ru';
$Password='P@ssw0rd';
$PasswordRepeat='P@ssw0rd';
$ProfArray=['Автомобильные дороги'];
$Agreement=true;

RegisterPage::of($I)->Register($FIO,$Email,$Password,$PasswordRepeat,$Phone,false,$ProfArray,[],[],false,false,false,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(4); } // only for selenium
$I->see(RegisterPage::$FormRegisterSuccess);

$I->amGoingTo('Проверяю что форма очистилась'); // ------------------------------------------------
RegisterPage::of($I)->CheckDefaultFormState();

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='admin';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю наличие созданной записи'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillTextFilter($FIO);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$ID=61;
$Status='требует проверки';
$Visible='показать';
$CurDate=date('d.m.Y',time());
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$Visible,$FIO,$Phone,$Email,$CurDate],$ID);