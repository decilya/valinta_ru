<?php

// @group unparalleled
// @group id188

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

$ID=26;
$Status='новая';
$FIO='Матвей Александрович Чернов';
$Comment='Тестовый комментарий к заявке №26.';

$I->amGoingTo('Нахожу анкету'); // ------------------------------------------------
AdminRequestListPage::of($I)->ApplyIDFilter($ID);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeRequests([$Status,$FIO],$ID);

$I->amGoingTo('Проверяю поле комментария'); // ------------------------------------------------
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);

$I->amGoingTo('Проверяю кнопку Обработана - отмена в форме'); // ------------------------------------------------
$Save=false;
AdminRequestListPage::of($I)->Handle($ID,$Save);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeRequests([$Status,$FIO],$ID);
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);

$I->amGoingTo('Проверяю кнопку Обработана - подтверждение в форме'); // ------------------------------------------------
$Save=true;
$Status='обработана';
AdminRequestListPage::of($I)->Handle($ID,$Save);
$cur_date=date('d.m.Y H:i:s',time());
$Comment='Заявка обработана в '. $cur_date .'; Тестовый комментарий к заявке №26.';
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminRequestListPage::of($I)->SeeRequests([$Status,$FIO],$ID);
AdminRequestListPage::of($I)->SeeComment($Comment,$ID);