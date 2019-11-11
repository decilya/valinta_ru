<?php

// @group parallel
// @group id60

use tests\codeception\_pages\RegisterPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку пустой формы на регистрации в базе');

$I->amGoingTo('Открываю страницу регитсрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RegisterPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------
$I->click(RegisterPage::$FormRegisterButton,RegisterPage::$FormRegisterName);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(RegisterPage::$FormRequiredFieldWarn);
$I->see(RegisterPage::$FormRegisterAgreementWarn);
$I->dontSee(RegisterPage::$FormRegisterSuccess);
