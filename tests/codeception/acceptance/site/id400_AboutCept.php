<?php
// @group site
// @group main-page
// @group id400

use tests\codeception\_pages\AboutPage;
use tests\codeception\_pages\MainPage;
use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\lkSmet4ikPage;
use tests\codeception\_controllers\CheckPages;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю счётчик опубликованных анкет - после скрытия анкеты');

$checkPages = new CheckPages($I);
$loginPage = new LoginPage($I);
$mainPage = MainPage::openBy($I);

$I->amGoingTo('Проверяю тексты на странице'); // ------------------------------------------------

$checkPages->checkMenu(MainPage::$TopMenuFindSmet4ikTexts,MainPage::$TopMenuProperty);
$checkPages->checkPageText(MainPage::$PageFindSmet4ikTexts);
$I->see(MainPage::$CounterText.MainPage::$CounterValue);

$I->amGoingTo('Открываю страницу о портале'); // ------------------------------------------------

$I->seeElement('//a[@'. AboutPage::$AboutLinkProperty .'][@href="'. AboutPage::$AboutLink .'"]');
$aboutPage = AboutPage::openBy($I);

$I->amGoingTo('Проверяю тексты на странице'); // ------------------------------------------------

$checkPages->checkMenu(AboutPage::$TopMenuTexts,AboutPage::$TopMenuProperty);
$checkPages->checkPageText(AboutPage::$PageTexts);

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------

$lksmet4ikPage = lkSmet4ikPage::openBy($I);

$checkPages->checkMenu(LoginPage::$TopMenuTexts,LoginPage::$TopMenuProperty);
$checkPages->checkPageText(LoginPage::$PageTexts);

$I->amGoingTo('Вхожу в личный кабинет'); // ------------------------------------------------

$loginPage->login('mjiquy_1991@xaker.ru','1qwe2qaz');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$checkPages->checkMenu(lkSmet4ikPage::$TopMenuLKTexts,lkSmet4ikPage::$TopMenuProperty);
$checkPages->checkPageText(lkSmet4ikPage::$PageLKTexts);
$ID=1;
$lksmet4ikPage->CheckPersonalID($ID);

$I->amGoingTo('Открываю страницу о портале'); // ------------------------------------------------

$I->seeElement('//a[@'. AboutPage::$AboutLinkProperty .'][@href="'. AboutPage::$AboutLink .'"]');
$aboutPage = AboutPage::openBy($I);

$I->amGoingTo('Проверяю тексты на странице'); // ------------------------------------------------

$checkPages->checkMenu(AboutPage::$TopMenuLKTexts,AboutPage::$TopMenuProperty);
$checkPages->checkPageText(AboutPage::$PageTexts);