<?php

// @group unparalleled
// @group id158

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\MainPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из системы управления - обязательные поля. Статус отклонена.');

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

$ID=8;
$Status='отклонена';
$WhyCancel='Проверяем функцию отклонения анкеты. Тестовая анкета №8.';
$Visible='показать';
$FIO='Яцков Андрей Арсениевич';
$Phone='+7(957)663-80-91';
$Email='jjigyu@email.ru';
$Exp='What WILL become of me?\' Luckily for Alice, the little door, so she.Ей-богу! да пребольно! Проснулся: черт возьми, дал. — Да на что устрица похожа..';
$City='Уинское (Пермский край)';
$IPAP='153726';
$Price='335214';
$Profs=['Бурение, скважины'];
$Docs=['Форма КС-6а'];
$Bases=['Индивидуальные/фирменные','ФЕР-2001'];

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);
AdminSmet4ikEditPage::of($I)->CheckStatus($Status,$Visible,$WhyCancel);

$I->amGoingTo('Очищаю лишние значения из селектов'); // ------------------------------------------------
AdminSmet4ikEditPage::of($I)->ClearCity();
AdminSmet4ikEditPage::of($I)->RemoveFromProfs($Profs);
AdminSmet4ikEditPage::of($I)->RemoveFromDocs($Docs);
AdminSmet4ikEditPage::of($I)->RemoveFromBases($Bases);

$I->amGoingTo('Проверяю отправку формы с обязательными значениями'); // ------------------------------------------------
$FIO='Петрова Татьяна Алексеевна';
$Phone='+7(957)663-80-91';
$Email='testuser@mail.ru';
$Exp='';
$City='';
$IPAP='';
$Price='';
$Profs=['Автомобильные дороги'];
$Docs=[];
$Bases=[];
AdminSmet4ikEditPage::of($I)->Update($FIO,$Email,$Phone,false,$Profs,$Docs,$Bases,$IPAP,$Price,$Exp);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormSuccess);
$I->reloadPage();

$I->amGoingTo('Проверяю что значения сохранились'); // ------------------------------------------------
AdminSmet4ikEditPage::of($I)->CheckStatus($Status,$Visible,$WhyCancel);
AdminSmet4ikEditPage::of($I)->CheckDefaultFormState($FIO,$Email,$Phone,$City,$Profs,$Docs,$Bases,$IPAP,$Price,$Exp);

$I->amGoingTo('Открываю главную страницу'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отсутствие отредактированной записи на главной странице'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();
MainPage::of($I)->DoNotSeeHuman([$FIO]);
