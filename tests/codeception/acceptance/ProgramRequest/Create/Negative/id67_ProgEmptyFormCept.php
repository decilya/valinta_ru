<?php

// @group parallel
// @group id67

use tests\codeception\_pages\ProgramRequestPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку пустой формы заявки на программу');

$I->amGoingTo('Открываю страницу заявки на программу'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отправку пустой формы'); // ------------------------------------------------
$I->click(ProgramRequestPage::$FormGetProgramButton,ProgramRequestPage::$FormGetProgramName);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
$I->see(ProgramRequestPage::$FormRequiredFieldWarn);
$I->see(ProgramRequestPage::$FormGetProgramAgreementWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);