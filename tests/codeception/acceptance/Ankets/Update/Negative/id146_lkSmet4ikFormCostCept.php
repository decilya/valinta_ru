<?php

// @group parallel
// @group id146

use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из личного кабинета - неверная стоимость.');

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

$Price='10000000';
lkSmet4ikPage::of($I)->Update(false,false,false,false,[],[],[],false,$Price,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormPriceWrongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Price='10 000 000';
lkSmet4ikPage::of($I)->Update(false,false,false,false,[],[],[],false,$Price,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormPriceNoDigWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Price='-900000';
lkSmet4ikPage::of($I)->Update(false,false,false,false,[],[],[],false,$Price,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormPriceWrongWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);

$Price='5o000';
lkSmet4ikPage::of($I)->Update(false,false,false,false,[],[],[],false,$Price,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormPriceNoDigWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);
