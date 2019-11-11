<?php

// @group cp
// @group cp-lists
// @group id385

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;
use tests\codeception\_controllers\CheckPages;
use tests\codeception\_controllers\AdminControllers;

// Исходные данные ------------------------------------------------
$anket53=['№53 - подтверждена','Изменена: 04.12.2010','Анкета: показана','ФИО: Шихранов Артём Якубович','Тел.: +7(921)179-83-46','E-mail: diuscheetur@mail.ru','Город: Мичуринск (Тамбовская область)'];
$anket1=['№1 - требует проверки','Изменена: 15.01.1970','Сметчик хочет: показать','ФИО: Унтилова Татьяна Алексеевна','Тел.: +7(921)925-26-94','E-mail: mjiquy_1991@xaker.ru','Город: Барыш (Ульяновская область)'];
$anket31=['№31 - требует проверки','Изменена: 10.06.2015','Сметчик хочет: показать','ФИО: Пьяныха Виктория Мироновна','Тел.: +7(984)519-60-99','E-mail: zheez@inbox.ru','Город: -'];
$anket50=['№50 - подтверждена','Изменена: 23.02.1972','Анкета: показана','ФИО: Трутнева Ксения Антониновна','Тел.: +7(986)249-70-30','E-mail: tiasout@rambler.ru','Город: -'];
$anket32=['№32 - отклонена','Изменена: 13.06.1971','Сметчик хочет: показать','ФИО: Корбылев Евстигней Карлович','Тел.: +7(914)703-12-21','E-mail: fymiomjo@quehiu.net','Город: -'];
$anket18=['№18 - отклонена','Изменена: 16.01.2014','Сметчик хочет: показать','ФИО: Русанова Анисья Юлиевна','Тел.: +7(955)998-47-31','E-mail: fymiomjo@google.com','Город: Черкесск (Республика Карачаево-Черкесия)'];
$anket34=['№34 - требует проверки','Изменена: 15.04.2011','Сметчик хочет: показать','ФИО: Буков Михаил Родионович','Тел.: +7(930)363-92-27','E-mail: sybiop@jaejjuz.net','Город: Афанасьево (Кировская область)'];
// ------------------------------------------------

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить работу фильтра анкет сметчиков в панели управления по статусу');

$loginPage = new LoginPage($I);
$checkPages = new CheckPages($I);
$adminControllers = new AdminControllers($I);
$adminSmet4ikListPage = AdminSmet4ikListPage::openBy($I);

$I->amGoingTo('Проверяю страницу'); // ------------------------------------------------

$checkPages->checkMenu(LoginPage::$TopMenuTexts,LoginPage::$TopMenuProperty);
$checkPages->checkPageText(LoginPage::$PageTexts);

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------

$loginPage->login('admin','1qwe2qaz');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$checkPages->checkMenu($adminSmet4ikListPage::$TopMenuTexts,$adminSmet4ikListPage::$TopMenuProperty);
$checkPages->checkPageText($adminSmet4ikListPage::$PageTexts);

$I->amGoingTo('Проверяю первую и последнюю записи'); // ------------------------------------------------

$loginPage->seeWorksheet($anket1,'1');
$adminControllers->GoToPage($adminSmet4ikListPage->GetLastPage(),AdminSmet4ikListPage::$PaginatorProperty);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$loginPage->seeWorksheet($anket53,'53');

$I->amGoingTo('Фильтрую по статусу'); // ------------------------------------------------

$FilterStatus='требует проверки';
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus]);

$I->amGoingTo('Проверяю первую и последнюю записи'); // ------------------------------------------------

$loginPage->seeWorksheet($anket1,'1');
$adminControllers->GoToPage($adminSmet4ikListPage->GetLastPage(),AdminSmet4ikListPage::$PaginatorProperty);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$loginPage->seeWorksheet($anket31,'31');

$I->amGoingTo('Фильтрую по статусу подтверждена'); // ------------------------------------------------

$FilterStatus='подтверждена';
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus]);

$I->amGoingTo('Проверяю первую и последнюю записи'); // ------------------------------------------------

$loginPage->seeWorksheet($anket50,'50');
$adminControllers->GoToPage($adminSmet4ikListPage->GetLastPage(),AdminSmet4ikListPage::$PaginatorProperty);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$loginPage->seeWorksheet($anket53,'53');

$I->amGoingTo('Фильтрую по статусу отклонена'); // ------------------------------------------------

$FilterStatus='отклонена';
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus]);

$I->amGoingTo('Проверяю первую и последнюю записи'); // ------------------------------------------------

$loginPage->seeWorksheet($anket32,'32');
$adminControllers->GoToPage($adminSmet4ikListPage->GetLastPage(),AdminSmet4ikListPage::$PaginatorProperty);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$loginPage->seeWorksheet($anket18,'18');

$I->amGoingTo('Фильтрую по статусу и ID'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='34';
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID]);
$loginPage->seeWorksheet($anket34,'34');

$I->amGoingTo('Фильтрую по несуществующим статусу и ID'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='100';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу и ID'); // ------------------------------------------------

$FilterStatus='подтверждена';
$FilterID='34';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по статусу и FIO'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='';
$FilterText='Буков Михаил Родионович';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$loginPage->seeWorksheet($anket34,'34');

$I->amGoingTo('Фильтрую по несуществующим статусу и FIO'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='';
$FilterText='Фамилия Имя Отчество';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу и FIO'); // ------------------------------------------------

$FilterStatus='отклонена';
$FilterID='';
$FilterText='Буков Михаил Родионович';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по статусу и E-mail'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='';
$FilterText='sybiop@jaejjuz.net';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$loginPage->seeWorksheet($anket34,'34');

$I->amGoingTo('Фильтрую по несуществующим статусу и E-mail'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='';
$FilterText='testmail@testmail.test';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу и E-mail'); // ------------------------------------------------

$FilterStatus='подтверждена';
$FilterID='';
$FilterText='sybiop@jaejjuz.net';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по статусу и Phone'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='';
$FilterText='+7(930)363-92-27';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$loginPage->seeWorksheet($anket34,'34');

$I->amGoingTo('Фильтрую по несуществующим статусу и Phone'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='';
$FilterText='+7(999)999-99-99';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу и Phone'); // ------------------------------------------------

$FilterStatus='отклонена';
$FilterID='';
$FilterText='+7(930)363-92-27';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по статусу, ID и FIO'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='34';
$FilterText='Буков Михаил Родионович';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$loginPage->seeWorksheet($anket34,'34');

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и FIO'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='100';
$FilterText='Буков Михаил Родионович';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и FIO'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='34';
$FilterText='Фамилия Имя Отчество';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и FIO'); // ------------------------------------------------

$FilterStatus='подтверждена';
$FilterID='34';
$FilterText='Буков Михаил Родионович';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по статусу, ID и E-mail'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='34';
$FilterText='sybiop@jaejjuz.net';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$loginPage->seeWorksheet($anket34,'34');

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и E-mail'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='100';
$FilterText='sybiop@jaejjuz.net';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и E-mail'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='34';
$FilterText='testmail@testmail.test';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и E-mail'); // ------------------------------------------------

$FilterStatus='отклонена';
$FilterID='34';
$FilterText='sybiop@jaejjuz.net';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по статусу, ID и Phone'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='34';
$FilterText='+7(930)363-92-27';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$loginPage->seeWorksheet($anket34,'34');

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и Phone'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='100';
$FilterText='+7(930)363-92-27';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и Phone'); // ------------------------------------------------

$FilterStatus='требует проверки';
$FilterID='34';
$FilterText='+7(999)999-99-99';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);

$I->amGoingTo('Фильтрую по несуществующим статусу, ID и Phone'); // ------------------------------------------------

$FilterStatus='подтверждена';
$FilterID='34';
$FilterText='+7(930)363-92-27';
$adminControllers->CleanFilter([AdminSmet4ikListPage::$FilterIDField,AdminSmet4ikListPage::$FilterTextField]);
$adminControllers->fillFilter([AdminSmet4ikListPage::$FilterStatusField=>$FilterStatus,AdminSmet4ikListPage::$FilterIDField=>$FilterID,AdminSmet4ikListPage::$FilterTextField=>$FilterText]);
$I->see(AdminSmet4ikListPage::$FilterFail);