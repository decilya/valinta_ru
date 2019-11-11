<?php

// @group unparalleled
// @group id127

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\MainPage;
use tests\codeception\_controllers\AdminControllers;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить подтверждение анкеты из статуса отклонена');

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

$ID=48;
$Status='Отклонена';
$FIO='Фомина Мария Несторовна';

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$FIO],$ID);

$I->amGoingTo('Проверяю "отмену" подтверждения'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->Confirm($ID,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$FIO],$ID);

$I->amGoingTo('Проверяю подтверждение'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->Confirm($ID,true);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$Status='подтверждена';

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$FIO],$ID);

$I->amGoingTo('Выхожу из панели управления'); // ------------------------------------------------
AdminControllers::of($I)->Logout();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в личный кабинет'); // ------------------------------------------------
$username='voschyasyes@langoo.com';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю статусы в кабинете'); // ------------------------------------------------
lkSmet4ikPage::of($I)->CheckStatus($Status,'');

$I->amGoingTo('Открываю главную страницу'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$Position=6;
$I->amGoingTo('Проверяю наличие подтвержденние записи на главной странице'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();
MainPage::of($I)->SeeHuman([$FIO],$Position);