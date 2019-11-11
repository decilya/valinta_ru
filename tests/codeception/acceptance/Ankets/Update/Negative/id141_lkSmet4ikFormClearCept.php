<?php

// @group parallel
// @group id141

use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\LoginPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из личного кабинета - форма пустая.');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$ID=1;

$I->amGoingTo('Вхожу в личный кабинет'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Очищаю лишние значения из селектов'); // ------------------------------------------------
lkSmet4ikPage::of($I)->ClearCity();
lkSmet4ikPage::of($I)->ClearProfs();
lkSmet4ikPage::of($I)->ClearDocs();
lkSmet4ikPage::of($I)->ClearBases();

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------
lkSmet4ikPage::of($I)->Update('','','',false,[],[],[],'','','');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormRequiredFieldWarn);
$I->dontSee(lkSmet4ikPage::$FormSuccess);
