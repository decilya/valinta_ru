<?php

// @group parallel
// @group id720

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить в списке ссылку редактирования сметчика');

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

$ID=1;

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->FillIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->amGoingTo('Проверяю ссылку'); // ------------------------------------------------
$I->seeLink(AdminSmet4ikListPage::$EditSmet4ikLink,$I->getCurrentUrl().AdminSmet4ikEditPage::$URL.$ID);

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
AdminSmet4ikListPage::of($I)->EditSmet4ik($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);
