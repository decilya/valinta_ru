<?php

// @group parallel
// @group id142

use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из личного кабинета - одно пустое обязалельное поле.');

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

$I->amGoingTo('Проверяю на обязательные поля'); // ------------------------------------------------

$FIO='';
lkSmet4ikPage::of($I)->Update($FIO,false,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormRequiredFieldWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$Email='';
lkSmet4ikPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormRequiredFieldWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$Phone='';
lkSmet4ikPage::of($I)->Update(false,false,$Phone,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormRequiredFieldWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->ClearProfs();
lkSmet4ikPage::of($I)->Update(false,false,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormRequiredFieldWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);