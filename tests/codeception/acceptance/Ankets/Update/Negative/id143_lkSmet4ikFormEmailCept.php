<?php

// @group parallel
// @group id143

use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из личного кабинета - неверный e-mail.');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$ID=1;

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------

$Email='mail@mail';
lkSmet4ikPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormEmailWrongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmail@mailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
lkSmet4ikPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (64@65)');
$I->see(lkSmet4ikPage::$FormEmailLongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmailm@ailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
lkSmet4ikPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (65@64)');
$I->see(lkSmet4ikPage::$FormEmailLongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmailm@ilmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
lkSmet4ikPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (65@63)');
$I->see(lkSmet4ikPage::$FormEmailWrongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Email='mailmailmailmailmailmailmailmailmailmailmailmailmailmailmailmai@mailmailmailmailmailmailmailmailmailmailmailmailmailmailmail.mail';
lkSmet4ikPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->amGoingTo('Проверяю «E-mail» (63@65)');
$I->see(lkSmet4ikPage::$FormEmailWrongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Email='моя_почта@mail.mail';
lkSmet4ikPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormEmailWrongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Email='ciogje@hotbox.ru';
lkSmet4ikPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormEmailExistWarn);
$I->see('E-mail уже зарегистрирован');
$I->dontSee(lkSmet4ikPage::$FormSuccess);