<?php

// @group cp
// @group cp-lists
// @group id180

use tests\codeception\_pages\LoginPage;

// Исходные данные ------------------------------------------------
$anketID=34;
$Status='№'. $anketID .' - требует проверки';
$EditDate='Изменена: 15.04.2011';
$Visible='Сметчик хочет: показать';
$FIO='ФИО: Буков Михаил Родионович';
$Phone='Тел.: +7(930)363-92-27';
$Email='E-mail: sybiop@jaejjuz.net';
$City='Город: Афанасьево (Кировская область)';
$Prof='Профессиональная область: Бурение, скважины';
$Bases='Нормативные базы: Ведомственные';
$Docs='Сметная документация: -';
$IPAP='Номер профессионального аттестата ИПАП: 256839';
$Cost='Стоимость от: 658690';
// ------------------------------------------------

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить работу фильтра анкет сметчиков в панели управления');

$loginPage = LoginPage::openBy($I);
$loginPage->checkPage();

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------

$loginPage->login('admin','1qwe2qaz');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$loginPage->checkAdminPage();

$I->dontSee('Ничего не найдено.');

$I->amGoingTo('Нахожу и проверяю анкету по ID'); // ------------------------------------------------
$FilterID='34';
$FilterText='';

$loginPage->fillFilter($FilterID, $FilterText);
$loginPage->seeWorksheet([$Status,$EditDate,$Visible,$FIO,$Phone,$Email,$City,$Prof,$Bases,$Docs,$IPAP,$Cost],$anketID);
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID'); // ------------------------------------------------
$FilterID = 100;
$FilterText='';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по ФИО'); // ------------------------------------------------
$FilterID='';
$FilterText = 'Буков Михаил Родионович';

$loginPage->fillFilter($FilterID, $FilterText);
$loginPage->seeWorksheet([$Status,$EditDate,$Visible,$FIO,$Phone,$Email,$City,$Prof,$Bases,$Docs,$IPAP,$Cost],$anketID);
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ФИО'); // ------------------------------------------------
$FilterID='';
$FilterText = 'Фамилия Имя Отчество';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по E-mail'); // ------------------------------------------------
$FilterID='';
$FilterText = 'sybiop@jaejjuz.net';

$loginPage->fillFilter($FilterID, $FilterText);
$loginPage->seeWorksheet([$Status,$EditDate,$Visible,$FIO,$Phone,$Email,$City,$Prof,$Bases,$Docs,$IPAP,$Cost],$anketID);
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по неверному E-mail'); // ------------------------------------------------
$FilterID='';
$FilterText = 'testmail@testmail.test';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по телефону'); // ------------------------------------------------
$FilterID='';
$FilterText='+7(930)363-92-27';

$loginPage->fillFilter($FilterID, $FilterText);
$loginPage->seeWorksheet([$Status,$EditDate,$Visible,$FIO,$Phone,$Email,$City,$Prof,$Bases,$Docs,$IPAP,$Cost],$anketID);
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по неверному телефону'); // ------------------------------------------------
$FilterID='';
$FilterText = '+7(999)999-99-99';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по ID и ФИО'); // ------------------------------------------------
$FilterID=34;
$FilterText='Буков Михаил Родионович';

$loginPage->fillFilter($FilterID, $FilterText);
$loginPage->seeWorksheet([$Status,$EditDate,$Visible,$FIO,$Phone,$Email,$City,$Prof,$Bases,$Docs,$IPAP,$Cost],$anketID);
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному ФИО'); // ------------------------------------------------
$FilterID=100;
$FilterText='Буков Михаил Родионович';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному ФИО'); // ------------------------------------------------
$FilterID=34;
$FilterText='Фамилия Имя Отчество';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по ID и email'); // ------------------------------------------------
$FilterID=34;
$FilterText='sybiop@jaejjuz.net';

$loginPage->fillFilter($FilterID, $FilterText);
$loginPage->seeWorksheet([$Status,$EditDate,$Visible,$FIO,$Phone,$Email,$City,$Prof,$Bases,$Docs,$IPAP,$Cost],$anketID);
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному email'); // ------------------------------------------------
$FilterID=100;
$FilterText='sybiop@jaejjuz.net';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному email'); // ------------------------------------------------
$FilterID=34;
$FilterText='testmail@testmail.test';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по ID и телефону'); // ------------------------------------------------
$FilterID=34;
$FilterText='+7(930)363-92-27';

$loginPage->fillFilter($FilterID, $FilterText);
$loginPage->seeWorksheet([$Status,$EditDate,$Visible,$FIO,$Phone,$Email,$City,$Prof,$Bases,$Docs,$IPAP,$Cost],$anketID);
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по неверному ID и верному телефону'); // ------------------------------------------------
$FilterID=100;
$FilterText='+7(930)363-92-27';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
$loginPage->clearFilter($FilterID, $FilterText);

$I->amGoingTo('Нахожу и проверяю анкету по верному ID и неверному телефону'); // ------------------------------------------------
$FilterID=34;
$FilterText='+7(999)999-99-99';

$loginPage->fillFilter($FilterID, $FilterText);
$I->see('Ничего не найдено.');
