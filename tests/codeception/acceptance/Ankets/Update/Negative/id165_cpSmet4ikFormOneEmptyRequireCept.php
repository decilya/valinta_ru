<?php

// @group parallel
// @group id165

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_pages\AdminSmet4ikEditPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из системы управления - одно пустое обязалельное поле.');

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

$I->amGoingTo('Захожу в редактирование анкеты'); // ------------------------------------------------
$ID=1;
$I->amOnPage(AdminSmet4ikEditPage::$URL.$ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikEditPage::of($I)->FastCheckPage($ID);

$I->amGoingTo('Проверяю на обязательные поля'); // ------------------------------------------------

$FIO='';
AdminSmet4ikEditPage::of($I)->Update($FIO,false,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormRequiredFieldWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$Email='';
AdminSmet4ikEditPage::of($I)->Update(false,$Email,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormRequiredFieldWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$Phone='';
AdminSmet4ikEditPage::of($I)->Update(false,false,$Phone,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormRequiredFieldWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);

$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$Profs=['Другое','Отделочные работы','Охранно-пожарные системы','Пусконаладочные работы','Реконструкция зданий и сооружений','Ремонтные работы по текущему и кап. ремонту','Сети связи, видеонаблюдение','Фасадные работы'];
AdminSmet4ikEditPage::of($I)->RemoveFromProfs($Profs);
AdminSmet4ikEditPage::of($I)->Update(false,false,false,false,[],[],[],false,false,false);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(AdminSmet4ikEditPage::$FormRequiredFieldWarn);
$I->dontSee(AdminSmet4ikEditPage::$FormSuccess);
