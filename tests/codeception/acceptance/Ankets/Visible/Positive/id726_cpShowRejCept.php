<?php

// @group unparalleled
// @group id726

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_controllers\AdminControllers;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Опубликовать анкету из админки в статусе отклонена');

$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='admin';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->FastCheckPage();

$ID=51;
$Status='Отклонена';
$Visible='скрыть';
$FIO='Набойщикова Изабелла Ипполитовна';

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);
AdminSmet4ikEditPage::of($I)->CheckStatus($Status,$Visible,'');

$I->amGoingTo('Публикую анкету'); // ------------------------------------------------
AdminSmet4ikEditPage::of($I)->UnHide();

$I->amGoingTo('Перехожу в список анкет'); // ------------------------------------------------
$I->amOnPage(AdminSmet4ikListPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->FastCheckPage();

$Visible='показать';
$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->SeeHuman([$Status,$Visible,$FIO],$ID);

$I->amGoingTo('Выхожу из панели управления'); // ------------------------------------------------
AdminControllers::of($I)->Logout();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в личный кабинет'); // ------------------------------------------------
$username='xjiveeth@rambler.ru';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю статусы в кабинете'); // ------------------------------------------------
lkSmet4ikPage::of($I)->CheckStatus($Status,'');
$I->seeLink(lkSmet4ikPage::$HideSmet4ikLink);