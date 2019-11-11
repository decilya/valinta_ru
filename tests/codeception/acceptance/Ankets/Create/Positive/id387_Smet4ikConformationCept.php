<?php

// @group parallel
// @group id387

use tests\codeception\_pages\RegisterPage;
use tests\codeception\_pages\AgreementPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю пользовательское соглашение в форме регистрации в базе');

$I->amGoingTo('Открываю страницу регистрации в базе'); // ------------------------------------------------
$I->amOnPage(RegisterPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
RegisterPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю наличие ссылки на соглашение'); // ------------------------------------------------

$I->seeElement('//a[@target="_blank"][@href="'. AgreementPage::$URL .'"]');
$I->seeLink(RegisterPage::$FormRequiredAgreementLinkText);

$I->amGoingTo('Открываю пользовательское соглашение'); // ------------------------------------------------

$I->amOnPage(AgreementPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AgreementPage::of($I)->FastCheckPage();

