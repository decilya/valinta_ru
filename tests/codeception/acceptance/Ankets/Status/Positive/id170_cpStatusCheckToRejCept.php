<?php

// @group unparalleled
// @group id170

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\MainPage;
use tests\codeception\_controllers\AdminControllers;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить отклонение анкеты из статуса требует проверки');

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

$ID=3;
$Status='требует проверки';
$FIO='Негуторова Василиса Федоровна';
$Reason='Проверяем функцию отклонения анкеты. Тестовая анкета №3.';

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$FIO],$ID);

$I->amGoingTo('Проверяю "отмену" отклонения'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->Reject($ID,$Reason,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$FIO],$ID);

$I->amGoingTo('Проверяю отклонение'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->Reject($ID,$Reason,true);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$Status='Отклонена';

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$FIO],$ID);

$I->amGoingTo('Выхожу из панели управления'); // ------------------------------------------------
AdminControllers::of($I)->Logout();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в личный кабинет'); // ------------------------------------------------
$username='schyubiuxou@langoo.com';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю статусы в кабинете'); // ------------------------------------------------
lkSmet4ikPage::of($I)->CheckStatus($Status,$Reason);

$I->amGoingTo('Открываю главную страницу'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отсутствие отредактированной записи на главной странице'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();
MainPage::of($I)->DoNotSeeHuman([$FIO]);