<?php

// @group parallel
// @group id733

use tests\codeception\_pages\MainPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверка показа анкеты сметчика');

$I->amGoingTo('Открываю страницу списка сметчиков'); // ------------------------------------------------
$I->amOnPage(MainPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
MainPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю человека'); // ------------------------------------------------
$Position=6;
$FIO='Мананникова Виктория Родионовна';
$IPAP='499533';
$Price='383263 руб.';
$Exp='In a little of the Nile On every golden scale! \'How cheerfully he seems to like her, down here, that I should frighten them out again. Suddenly she came suddenly upon an open place,.';
$Profs='Мосты, эстакады, путепроводы; Пусконаладочные работы; Теплоснабжение; Садово-парковое и ландшафтное проектирование; Другое; Сети связи, видеонаблюдение; Кровельные работы; Благоустройство, озеленение; Конструкции железобетонные; Фасадные работы';
$Bases='Ведомственные; ТСНБ-2001; ТСН-2001 Москва; ТЕР-2001; Индивидуальные/фирменные; ПИР; Прочее';
$Docs='Форма КС-3; Тендерная документация; Локальная смета';
MainPage::of($I)->CheckAllFieldsText($Position);
MainPage::of($I)->SeeHuman([$FIO,$IPAP,$Price,$Exp,$Profs,$Bases,$Docs],$Position);
