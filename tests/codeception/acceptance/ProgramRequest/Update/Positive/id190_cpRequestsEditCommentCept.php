<?php

// @group unparalleled
// @group id190

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminRequestListPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить добавление комментария к заявке');

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

$ID=28;
$FIO='Щербаков Богдан Евгеньевич';
$Comment='Тестовый комментарий к заявке №28.';

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminRequestListPage::of($I)->ApplyIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeRequests([$FIO],$ID);

$I->amGoingTo('Проверяю поле комментария'); // ------------------------------------------------
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);

$I->amGoingTo('Проверяю кнопку Отмену редактирования - комментарий не меняем'); // ------------------------------------------------
$Save=false;
$NewComment=false;
AdminRequestListPage::of($I)->EditComment($NewComment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);

$I->amGoingTo('Проверяю кнопку Сохранения редактирования - комментарий не меняем'); // ------------------------------------------------
$Save=true;
$NewComment=false;
AdminRequestListPage::of($I)->EditComment($NewComment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);

$I->amGoingTo('Проверяю кнопку Отмену редактирования - комментарий меняем'); // ------------------------------------------------
$Save=false;
$NewComment='Тестовый комментарий.';
AdminRequestListPage::of($I)->EditComment($NewComment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);

$I->amGoingTo('Проверяю кнопку Сохранения редактирования - комментарий меняем'); // ------------------------------------------------
$Save=true;
$NewComment='Тестовый комментарий.';
AdminRequestListPage::of($I)->EditComment($NewComment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($NewComment,$ID);

$I->amGoingTo('Проверяю кнопку Отмену редактирования - комментарий очищаем'); // ------------------------------------------------
$Save=false;
$Comment='Тестовый комментарий.';
$NewComment='';
AdminRequestListPage::of($I)->EditComment($NewComment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);

$I->amGoingTo('Проверяю кнопку Сохранения редактирования - комментарий очищаем'); // ------------------------------------------------
$Save=true;
$NewComment='';
AdminRequestListPage::of($I)->EditComment($NewComment,$ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeComment($NewComment,$ID);