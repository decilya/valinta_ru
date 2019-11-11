<?php

// @group unparalleled
// @group id66

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\ProgramRequestPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminRequestListPage;
use tests\codeception\_controllers\Smet4ikControllers;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку формы заявки на программу из личного кабинета');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в кабинет'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1qwe2qaz';
$userID=1;
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($userID);

$I->amGoingTo('Открываю страницу страницу заявки после входа в ЛК'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отправку формы со значениями по умолчанию'); // ------------------------------------------------
$Agreement=true;
ProgramRequestPage::of($I)->SendRequest(false,false,false,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramSuccess);

$I->reloadPage();

$I->amGoingTo('Проверяю отправку формы с новыми'); // ------------------------------------------------
$FIO='Test User';
$Email='testuser@mail.ru';
$Phone='+7(999)888-77-44';
$Agreement=true;
ProgramRequestPage::of($I)->SendRequest($FIO,$Email,$Phone,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(3); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramSuccess);

$I->amGoingTo('Проверяю, что форма принимает исходное значение'); // ------------------------------------------------
$FIO='Унтилова Татьяна Алексеевна';
$Email='mjiquy_1991@xaker.ru';
$Phone='+7(921)925-26-94';
$Agreement=false;
ProgramRequestPage::of($I)->CheckDefaultFormState($FIO,$Email,$Phone,$Agreement);

$I->amGoingTo('Выхожу из личного кабинета'); // ------------------------------------------------
Smet4ikControllers::of($I)->Logout();
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

$I->amGoingTo('Проверяю созданные записи'); // ------------------------------------------------
$cur_date=date('d.m.Y',time());
$ID=32;
$FIO='Test User';
$Email='testuser@mail.ru';
$Phone='+7(999)888-77-44';
$Status='новая';
AdminRequestListPage::of($I)->seeRequests([$FIO,$Email,$Phone,$cur_date],$ID);

$ID=31;
$FIO='Унтилова Татьяна Алексеевна';
$Email='mjiquy_1991@xaker.ru';
$Phone='+7(921)925-26-94';
$Status='новая';
AdminRequestListPage::of($I)->seeRequests([$FIO,$Email,$Phone,$cur_date],$ID);

