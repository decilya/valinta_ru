<?php

// @group parallel
// @group id181

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminRequestListPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить работу фильтра анкет сметчиков в панели управления');

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

$ID=23;
$Number='№'. $ID;
$Date='27.02.2008 21:51:07';
$FIO='Капитолина Фёдоровна Овчинникова';
$Phone='+7(936)398-40-79';
$Email='an.ignatev@list.ru';
$Status='обработана';

$FilterStatus='все';
$I->amGoingTo('Нахожу и проверяю анкету по ID. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID = 100;
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'Фамилия Имя Отчество';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по E-mail. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному E-mail. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'testmail@testmail.test';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = '+7(999)999-99-99';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='Фамилия Имя Отчество';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='testmail@testmail.test';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='+7(999)999-99-99';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);


$FilterStatus='обработана';
$I->amGoingTo('Нахожу и проверяю анкету по ID. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID = 100;
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'Фамилия Имя Отчество';
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по E-mail. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному E-mail. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'testmail@testmail.test';
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = '+7(999)999-99-99';
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='Фамилия Имя Отчество';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='testmail@testmail.test';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='+7(999)999-99-99';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);


$FilterStatus='новая';
$I->amGoingTo('Нахожу и проверяю анкету по ID. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID = 100;
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'Фамилия Имя Отчество';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по E-mail. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному E-mail. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'testmail@testmail.test';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = '+7(999)999-99-99';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='Капитолина Фёдоровна Овчинникова';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='Фамилия Имя Отчество';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='an.ignatev@list.ru';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='testmail@testmail.test';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='+7(936)398-40-79';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=23;
$FilterText='+7(999)999-99-99';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$ID=24;
$Date='10.06.2009 10:58:26';
$FIO='Королёва Владлена Романовна';
$Phone='+7(994)216-49-88';
$Email='mihail94@bykov.com';
$Status='новая';

$I->amGoingTo('Нахожу и проверяю анкету по ID. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=24;
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'Королёва Владлена Романовна';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по E-mail. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText = 'mihail94@bykov.com';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterText='+7(994)216-49-88';
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=24;
$FilterText = 'Королёва Владлена Романовна';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText = 'Королёва Владлена Романовна';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному ФИО. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=24;
$FilterText='Фамилия Имя Отчество';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=24;
$FilterText = 'mihail94@bykov.com';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='mihail94@bykov.com';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному email. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=24;
$FilterText='testmail@testmail.test';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по ID и телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=24;
$FilterText='+7(994)216-49-88';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
AdminRequestListPage::of($I)->SeeRequests([$Date,$FIO,$Phone,$Email,$Status],$ID);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=100;
$FilterText='+7(994)216-49-88';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному телефону. Статус - '. $FilterStatus); // ------------------------------------------------
$FilterID=24;
$FilterText='+7(999)999-99-99';
AdminRequestListPage::of($I)->ApplyIDFilter($FilterID);
AdminRequestListPage::of($I)->ApplyTextFilter($FilterText);
AdminRequestListPage::of($I)->ApplyStatusFilter($FilterStatus);
$I->see(AdminRequestListPage::$NoRecordsFind);
$I->amOnPage(AdminRequestListPage::$URL);