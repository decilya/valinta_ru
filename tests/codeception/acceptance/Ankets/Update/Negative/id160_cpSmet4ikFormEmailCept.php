<?php

// @group parallel
// @group id160

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из системы управления - неверный e-mail.');

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

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$ID=1;
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------

$Email='mail@mail';
AdminSmet4ikEditPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormEmailWrongWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmail@mailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
AdminSmet4ikEditPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (64@65)');
$I->see(AdminSmet4ikEditPage::$FormEmailLongWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmailm@ailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
AdminSmet4ikEditPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (65@64)');
$I->see(AdminSmet4ikEditPage::$FormEmailLongWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmailm@ilmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
AdminSmet4ikEditPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (65@63)');
$I->see(AdminSmet4ikEditPage::$FormEmailWrongWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmai@mailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
AdminSmet4ikEditPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (63@65)');
$I->see(AdminSmet4ikEditPage::$FormEmailWrongWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$Email='моя_почта@mail.mail';
AdminSmet4ikEditPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormEmailWrongWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$Email='ciogje@hotbox.ru';
AdminSmet4ikEditPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormEmailExistWarn);
$I->see('E-mail уже зарегистрирован');
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

