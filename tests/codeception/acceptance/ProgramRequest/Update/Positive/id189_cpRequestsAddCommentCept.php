<?php

// @group unparalleled
// @group id189

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminRequestListPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить добавление комментария');

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

$I->amGoingTo('Перехожу на страницу заявок'); // ------------------------------------------------
$I->amOnPage(AdminRequestListPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->FastCheckPage();

$ID=19;
$FIO='Цветков Марат Алексеевич';
$EmptyComment='';
$Comment='Тестовый комментарий к заявке №19.';

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminRequestListPage::of($I)->ApplyIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeRequests([$FIO],$ID);

$I->amGoingTo('Проверяю поле комментария'); // ------------------------------------------------
AdminRequestListPage::of($I)->SeeComment($EmptyComment,$ID);

$I->amGoingTo('Проверяю кнопку Отмену редактирования - пустой комментарий'); // ------------------------------------------------
$Save=false;
AdminRequestListPage::of($I)->EditComment($EmptyComment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($EmptyComment,$ID);

$I->amGoingTo('Проверяю кнопку Сохранение редактирования - пустой комментарий'); // ------------------------------------------------
$Save=true;
$I->see($ID);
AdminRequestListPage::of($I)->EditComment($EmptyComment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($EmptyComment,$ID);

$I->amGoingTo('Проверяю кнопку Отмену редактирования - комментарий заполнен'); // ------------------------------------------------
$Save=false;
AdminRequestListPage::of($I)->EditComment($Comment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($EmptyComment,$ID);

$I->amGoingTo('Проверяю кнопку Сохранение редактирования - комментарий заполнен'); // ------------------------------------------------
$Save=true;
AdminRequestListPage::of($I)->EditComment($Comment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);