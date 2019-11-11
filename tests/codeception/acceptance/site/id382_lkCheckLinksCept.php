<?php

// @group site
// @group lk-auth
// @group id382

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\MainPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_controllers\CheckPages;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить ссылки верхнего меню из кабинета сметчика');

$checkPages = new CheckPages($I);
$loginPage = new LoginPage($I);
$mainPage = new MainPage($I);
$lksmet4ikPage = lkSmet4ikPage::openBy($I);

$checkPages->checkMenu(LoginPage::$TopMenuTexts,LoginPage::$TopMenuProperty);
$checkPages->checkPageText(LoginPage::$PageTexts);

$I->amGoingTo('Вхожу в кабинет сметчика'); // ------------------------------------------------

$loginPage->login('mjiquy_1991@xaker.ru','1qwe2qaz');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$checkPages->checkMenu(lkSmet4ikPage::$TopMenuLKTexts,lkSmet4ikPage::$TopMenuProperty);
$checkPages->checkPageText(lkSmet4ikPage::$PageLKTexts);
$ID=1;
$lksmet4ikPage->CheckPersonalID($ID);

$I->amGoingTo('Проверяю ссылку текущей страницы'); // ------------------------------------------------

$I->click(lkSmet4ikPage::$LinkGetProgram);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$checkPages->checkMenu(lkSmet4ikPage::$TopMenuLKTexts,lkSmet4ikPage::$TopMenuProperty);
$checkPages->checkPageText(lkSmet4ikPage::$PageLKTexts);

$I->amGoingTo('Перехожу на ссылку поиска сметчиков'); // ------------------------------------------------

$I->click(lkSmet4ikPage::$LinkFindSmet4ik);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$checkPages->checkMenu($mainPage::$TopMenuFindSmet4ikTextsLK,$mainPage::$TopMenuProperty);
$checkPages->checkPageText($mainPage::$PageFindSmet4ikTexts);

$I->amGoingTo('Перехожу ссылку Личного кабинета'); // ------------------------------------------------

$I->click(lkSmet4ikPage::$LinkLK);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$checkPages->checkMenu(lkSmet4ikPage::$TopMenuLKTexts,lkSmet4ikPage::$TopMenuProperty);
$checkPages->checkPageText(lkSmet4ikPage::$PageLKTexts);

$I->amGoingTo('Перехожу на ссылку поиска сметчиков'); // ------------------------------------------------

$I->click(lkSmet4ikPage::$LinkFindSmet4ik);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$checkPages->checkMenu($mainPage::$TopMenuFindSmet4ikTextsLK,$mainPage::$TopMenuProperty);
$checkPages->checkPageText($mainPage::$PageFindSmet4ikTexts);

$I->amGoingTo('Перехожу на ссылку заказа сметной программы'); // ------------------------------------------------

$I->click(lkSmet4ikPage::$LinkGetProgram);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$checkPages->checkMenu(lkSmet4ikPage::$TopMenuLKTexts,lkSmet4ikPage::$TopMenuProperty);
$checkPages->checkPageText(lkSmet4ikPage::$PageLKTexts);

