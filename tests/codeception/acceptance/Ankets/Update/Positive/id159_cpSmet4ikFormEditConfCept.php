<?php

// @group unparalleled
// @group id159

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\MainPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из системы управления - обязательные поля. Статус подтверждена.');

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

$ID=50;
$Status='подтверждена';
$WhyCancel='';
$Visible='показана';
$FIO='Трутнева Ксения Антониновна';
$Phone='+7(986)249-70-30';
$Email='tiasout@rambler.ru';
$Exp='I the same solemn.Чай, — в Москве торговал, одного оброку приносил — по восьми гривен за душу, только ассигнациями, право только для знакомства! «Что он в собственном экипаже по бесконечно широким улицам, озаренным тощим освещением из кое-где мелькавших океан. Впрочем, губернаторский дом был так освещен, хоть бы и сами, потому что хрипел, как.';
$City='Уинское (Пермский край)';
$IPAP='766353';
$Price='266225';
$Profs=['Конструкции железобетонные','Земляные работы','Геодезические работы','Благоустройство, озеленение','Электромонтажные работы'];
$Docs=['Сводный сметный расчет/Объектная смета','Форма КС-3'];
$Bases=['ТЕР-2001','Ведомственные'];

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);
AdminSmet4ikEditPage::of($I)->CheckStatus($Status,$Visible,$WhyCancel);

$I->amGoingTo('Очищаю лишние значения из селектов'); // ------------------------------------------------
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

$I->amGoingTo('Проверяю налисие отредактированной записи на главной странице'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();
$Position=10;
MainPage::of($I)->SeeHuman([$FIO,implode("; ",$Profs)],$Position);
MainPage::of($I)->SeeContact($Phone,$Email,$Position);
