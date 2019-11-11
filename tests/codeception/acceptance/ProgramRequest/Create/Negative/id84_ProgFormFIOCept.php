<?php

// @group parallel
// @group id84

use tests\codeception\_pages\ProgramRequestPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверяю на отправку формы заявки на программу с неправильным FIO');

$I->amGoingTo('Открываю страницу заявки на программу'); // ------------------------------------------------
$I->amOnPage(ProgramRequestPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
ProgramRequestPage::of($I)->FastCheckPage();

$I->amGoingTo('Проверяю отправку формы'); // ------------------------------------------------

$FIO='A3A5A7A10A13A16A19A22A25A28A31A34A37A40A43A46A49A52A55A58A61A64A67A70A73A76A79A82A85A88A91A94A97A101A';
$Email='mail@mail.mail';
$Phone='+7(999)888-77-44';
$Agreement=true;
ProgramRequestPage::of($I)->SendRequest($FIO,$Email,$Phone,$Agreement);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$I->see(ProgramRequestPage::$FormGetProgramFIOWrongWarn);
$I->dontSee(ProgramRequestPage::$FormGetProgramSuccess);
