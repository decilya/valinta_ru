<?php

// @group parallel
// @group id83

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\ProgramRequestPage;
use tests\codeception\_pages\lkSmet4ikPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку формы заявки на программу с неверным ФИО из личного кабинета');

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
$FIO='A3A5A7A10A13A16A19A22A25A28A31A34A37A40A43A46A49A52A55A58A61A64A67A70A73A76A79A82A85A88A91A94A97A101A';
$Agreement=true;
ProgramRequestPage::of($I)->SendRequest($FIO,false,false,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramFIOWrongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
