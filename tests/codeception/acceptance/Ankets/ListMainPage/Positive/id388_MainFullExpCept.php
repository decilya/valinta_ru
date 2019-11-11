<?php

// @group parallel
// @group id388

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка "Читать полностью"');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$Position=1;
$FIO='Яльцев Егор Климентович';
$Exp='Чичиков только заметил сквозь густое покрывало лившего дождя что-то';
$ExpFull='Чичиков только заметил сквозь густое покрывало лившего дождя что-то похожее на все то, что разлучили их с приятелями, или просто проживающая в доме: что-то без чепца, около тридцати лет, в пестром платке. Есть.';

$I->amGoingTo('Проверяю человека'); // ------------------------------------------------
MainPage::of($I)->SeeHuman([$FIO,$Exp],$Position);

$I->amGoingTo('Проверяю полный текст опыта'); // ------------------------------------------------
MainPage::of($I)->SeeFullExperience($ExpFull,$Position);
