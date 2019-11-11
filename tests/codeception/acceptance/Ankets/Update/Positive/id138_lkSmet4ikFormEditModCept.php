<?php

// @group unparalleled
// @group id138

use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю редактирование формы сметчика из личного кабинета - обязательные поля. Статус на модерации.');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$ID=1;
$Status='на модерации';

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='mjiquy_1991@xaker.ru';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
lkSmet4ikPage::of($I)->FastCheckPage($ID);
lkSmet4ikPage::of($I)->CheckStatus($Status,'');

$I->amGoingTo('Очищаю лишние значения из селектов'); // ------------------------------------------------
lkSmet4ikPage::of($I)->ClearCity();
lkSmet4ikPage::of($I)->ClearProfs();
lkSmet4ikPage::of($I)->ClearDocs();
lkSmet4ikPage::of($I)->ClearBases();

$I->amGoingTo('Проверяю отправку формы с обязательными значениями'); // ------------------------------------------------
$FIO='Петрова Татьяна Алексеевна';
$Phone='+7(999)925-26-94';
$Email='testuser@mail.ru';
$Exp='';
$City='';
$IPAP='';
$Price='';
$Profs=['Автомобильные дороги'];
$Docs=[];
$Bases=[];
lkSmet4ikPage::of($I)->Update($FIO,$Email,$Phone,false,$Profs,$Docs,$Bases,$IPAP,$Price,$Exp);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(lkSmet4ikPage::$FormSuccess);
$I->reloadPage();

$I->amGoingTo('Проверяю что значения сохранились'); // ------------------------------------------------
lkSmet4ikPage::of($I)->CheckStatus($Status,'');
lkSmet4ikPage::of($I)->CheckDefaultFormState($FIO,$Email,$Phone,$City,$Profs,$Docs,$Bases,$IPAP,$Price,$Exp);

$I->amGoingTo('Открываю главную страницу'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отсутствие отредактированной записи на главной странице'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();
MainPage::of($I)->DoNotSeeHuman([$FIO]);