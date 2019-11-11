<?php

// @group parallel
// @group id725

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка показа анкеты сметчика в зависимости от его статуса и видимости');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Загружаю всех сметчиков'); // ------------------------------------------------
MainPage::of($I)->LoadAllPages();

$I->amGoingTo('Проверяю анкету - (Отклонена/Показана)'); // ------------------------------------------------
$FIO='Фомина Мария Несторовна';
MainPage::of($I)->DoNotSeeHuman([$FIO]);

$I->amGoingTo('Проверяю анкету - (Подтверждена/Показана)'); // ------------------------------------------------
$Position=8;
$FIO='Пьяныха Светлана Антониновна';
MainPage::of($I)->SeeHuman([$FIO],$Position);

$I->amGoingTo('Проверяю анкету - (Требует проверки/Показана)'); // ------------------------------------------------
$Position=1;
$FIO='Негуторова Василиса Федоровна';
MainPage::of($I)->DoNotSeeHuman([$FIO]);

$I->amGoingTo('Проверяю анкету - (Отклонена/Скрыта)'); // ------------------------------------------------
$Position=1;
$FIO='Набойщикова Изабелла Ипполитовна';
MainPage::of($I)->DoNotSeeHuman([$FIO]);

$I->amGoingTo('Проверяю анкету - (Подтверждена/Скрыта)'); // ------------------------------------------------
$Position=1;
$FIO='Шихранов Артём Якубович';
MainPage::of($I)->DoNotSeeHuman([$FIO]);

$I->amGoingTo('Проверяю анкету - (Требует проверки/Скрыта)'); // ------------------------------------------------
$Position=1;
$FIO='Подмазко Виктория Геннадиевна';
MainPage::of($I)->DoNotSeeHuman([$FIO]);


