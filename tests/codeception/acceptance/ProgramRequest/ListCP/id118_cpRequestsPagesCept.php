<?php

// @group parallel
// @group id118

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminRequestListPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить сортировку запросов программы по умолчанию в панели управления');

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

$I->amGoingTo('Проверяю наличия пагинатора - минимум д.б. 3-и страницы'); // ------------------------------------------------

$Pages=AdminRequestListPage::of($I)->GetLastPage();
if ($Pages < 3) $I->see('Должно быть не меньше 3-х страниц');

$I->amGoingTo('Последовательно перехожу на последнюю страницу'); // ------------------------------------------------

for($i=1; $i<$Pages; $i++) {
    AdminRequestListPage::of($I)->NextPage();
    if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
}

$ID=1;
$Time='26.02.1976 03:36:18';
$Phone='+7(973)837-54-36';
$Email='denisov.inga@ya.ru';
$FIO='Белоусов Богдан Сергеевич';
AdminRequestListPage::of($I)->SeeRequests([$Time,$Phone,$Email,$FIO],$ID);

$I->amGoingTo('Последовательно перехожу на первую страницу'); // ------------------------------------------------

for($i=$Pages-1; $i>0; $i--) {
    AdminRequestListPage::of($I)->PreviousPage();
    if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
}

$ID=30;
$Time='12.01.2016 05:48:20';
$Phone='+7(980)607-36-62';
$Email='kpetrov@ya.ru';
$FIO='Кабанова Лариса Сергеевна';
AdminRequestListPage::of($I)->SeeRequests([$Time,$Phone,$Email,$FIO],$ID);
