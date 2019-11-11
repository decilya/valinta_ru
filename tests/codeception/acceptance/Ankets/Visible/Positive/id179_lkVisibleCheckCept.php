<?php

// @group unparalleled
// @group id179

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_controllers\Smet4ikControllers;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить видимость анкеты требует проверки. Управляет сметчик.');
$I->wantTo('Скрыть анкету из личного кабинета в требует проверки');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$ID=3;
$Status='на модерации';
$Visible='скрыть';
$FIO='Негуторова Василиса Федоровна';

$I->amGoingTo('Вхожу в личный кабинет'); // ------------------------------------------------
$username='schyubiuxou@langoo.com';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю статусы в кабинете'); // ------------------------------------------------
lkSmet4ikPage::of($I)->CheckStatus($Status,'');
$I->seeLink(lkSmet4ikPage::$HideSmet4ikLink);

$I->amGoingTo('Скрываю анкету'); // ------------------------------------------------
lkSmet4ikPage::of($I)->Hide();

$I->amGoingTo('Выхожу из кабинета сметчика'); // ------------------------------------------------
Smet4ikControllers::of($I)->Logout();
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='admin';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->FastCheckPage();

$Status='требует проверки';
$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$Visible,$FIO],$ID);

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);
AdminSmet4ikEditPage::of($I)->CheckStatus($Status,$Visible,'');
