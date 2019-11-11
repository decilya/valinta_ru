<?php

// @group cp
// @group cp-lists
// @group id184

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\Smet4ikPage;

// Исходные данные ------------------------------------------------
$RecordsInBase = 60;
// ------------------------------------------------

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить кнопку Вернуться из редактирования анкеты');

$smet4ikPage = new Smet4ikPage($I);
$loginPage = LoginPage::openBy($I);
$loginPage->checkPage();

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------

$loginPage->login('admin','1qwe2qaz');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
$loginPage->checkAdminPage();

$I->amGoingTo('Рассчитываю параметры страницы'); // ------------------------------------------------

$RecordsOnPage = $I->grabAttributeFrom('div[class="resultsBlock"]','data-results-limit');
$Pages = ceil($RecordsInBase/$RecordsOnPage);

$I->amGoingTo('Проверяю наличия пагинатора - минимум д.б. 3-и страницы'); // ------------------------------------------------

$I->see('1','div[class="paginator"]');
$I->see('2','div[class="paginator"]');
$I->see('3','div[class="paginator"]');

$I->amGoingTo('Последовательно перехожу на последнюю страницу'); // ------------------------------------------------

$I->click((string)$Pages,'div[class="paginator"]');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$anketID=53;
$Status='подтверждена';
$Visible='показана';
$Date='04.12.2010';
$FIO='Шихранов Артём Якубович';
$Phone='+7(921)179-83-46';
$Email='diuscheetur@mail.ru';
$City='Мичуринск (Тамбовская область)';
$IPAP='936514';
$Price='248622';
$Prof='Реставрационные работы; Газоснабжение; Водоснабжение и водоотведение; Сети связи, видеонаблюдение; Архитектурные решения; Земляные работы';
$ProfArray=['Реставрационные работы','Газоснабжение','Водоснабжение и водоотведение','Сети связи, видеонаблюдение','Архитектурные решения','Земляные работы'];
$Bases='-';
$BasesArray=[''];
$SmetnDocs='Сводный сметный расчет/Объектная смета';
$SmetnDocsArray=['Сводный сметный расчет/Объектная смета'];

$loginPage->seeWorksheet(['№'. $anketID .' - '. $Status,'Изменена: '. $Date,'Анкета: '. $Visible,'ФИО: '. $FIO,'Тел.: '. $Phone,'E-mail: '. $Email,'Город: '. $City,'Профессиональная область: '. $Prof,'Нормативные базы: '. $Bases,'Сметная документация: '. $SmetnDocs,'Номер профессионального аттестата ИПАП: '. $IPAP,'Стоимость от: '. $Price .''],$anketID);

$I->amGoingTo('Захожу в редактирование анкеты и проверяю верхнюю ссылку'); // ------------------------------------------------

$I->click('Редактировать','div[data-id="'. $anketID .'"]');
if (method_exists($I, 'wait')) { $I->wait(2); } // only for selenium

$smet4ikPage->checkFormSmet4ik('admin-edit');
$I->see('Статус анкеты: '. $Status);
$I->see('Анкета: '. $Visible);
$I->seeInFormFields('form[name="registrationForm"]',['User[fio]'=>$FIO,'User[email]'=>$Email,'User[phone]'=>$Phone,'User[price]'=>$Price,'User[ipap_attestat_id]'=>$IPAP]);
$smet4ikPage->checkValueinSelect(['user-city_id'=>[$City],'user-professions required'=>$ProfArray,'user-smetadocs'=>$SmetnDocsArray,'user-normbases'=>$BasesArray]);

$I->amGoingTo('Проверяю ссылки после обновления'); // ------------------------------------------------

$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(2); } // only for selenium
$I->seeElement('//a[@class="backToIndex top"][@href="/user/index?page='. $Pages .'#item_'. $anketID .'"]');
$I->seeElement('//a[@class="backToIndex bottom"][@href="/user/index?page='. $Pages .'#item_'. $anketID .'"]');

$I->amGoingTo('Возвращаюсь в список - проверяю верхнюю ссылку'); // ------------------------------------------------

$I->click('a[class="backToIndex top"]');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$loginPage->seeWorksheet(['№'. $anketID .' - '. $Status,'Изменена: '. $Date,'Анкета: '. $Visible,'ФИО: '. $FIO,'Тел.: '. $Phone,'E-mail: '. $Email,'Город: '. $City,'Профессиональная область: '. $Prof,'Нормативные базы: '. $Bases,'Сметная документация: '. $SmetnDocs,'Номер профессионального аттестата ИПАП: '. $IPAP,'Стоимость от: '. $Price .''],$anketID);

$I->amGoingTo('Захожу в редактирование анкеты и проверяю нижнюю ссылку'); // ------------------------------------------------

$I->click('Редактировать','div[data-id="'. $anketID .'"]');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$smet4ikPage->checkFormSmet4ik('admin-edit');
$I->see('Статус анкеты: '. $Status);
$I->see('Анкета: '. $Visible);
$I->seeInFormFields('form[name="registrationForm"]',['User[fio]'=>$FIO,'User[email]'=>$Email,'User[phone]'=>$Phone,'User[price]'=>$Price,'User[ipap_attestat_id]'=>$IPAP]);
$smet4ikPage->checkValueinSelect(['user-city_id'=>[$City],'user-professions required'=>$ProfArray,'user-smetadocs'=>$SmetnDocsArray,'user-normbases'=>$BasesArray]);

$I->amGoingTo('Возвращаюсь в список - проверяю нижнюю ссылку'); // ------------------------------------------------

$I->click('a[class="backToIndex bottom"]');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$loginPage->seeWorksheet(['№'. $anketID .' - '. $Status,'Изменена: '. $Date,'Анкета: '. $Visible,'ФИО: '. $FIO,'Тел.: '. $Phone,'E-mail: '. $Email,'Город: '. $City,'Профессиональная область: '. $Prof,'Нормативные базы: '. $Bases,'Сметная документация: '. $SmetnDocs,'Номер профессионального аттестата ИПАП: '. $IPAP,'Стоимость от: '. $Price .''],$anketID);

$I->amGoingTo('Захожу в редактирование анкеты по ссылке'); // ------------------------------------------------

$I->amOnPage('/user/update/53');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$smet4ikPage->checkFormSmet4ik('admin-edit');
$I->see('Статус анкеты: '. $Status);
$I->see('Анкета: '. $Visible);
$I->seeInFormFields('form[name="registrationForm"]',['User[fio]'=>$FIO,'User[email]'=>$Email,'User[phone]'=>$Phone,'User[price]'=>$Price,'User[ipap_attestat_id]'=>$IPAP]);
$smet4ikPage->checkValueinSelect(['user-city_id'=>[$City],'user-professions required'=>$ProfArray,'user-smetadocs'=>$SmetnDocsArray,'user-normbases'=>$BasesArray]);

$I->amGoingTo('Проверяю ссылки после обновления'); // ------------------------------------------------

$I->reloadPage();
if (method_exists($I, 'wait')) { $I->wait(2); } // only for selenium
$I->seeElement('//a[@class="backToIndex top"][@href="/user/index"]');
$I->seeElement('//a[@class="backToIndex bottom"][@href="/user/index"]');

$I->amGoingTo('Возвращаюсь в список - проверяю верхнюю ссылку'); // ------------------------------------------------

$I->click('a[class="backToIndex top"]');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->see('№1 - требует проверки','div[data-id="1"]');

$I->amGoingTo('Захожу в редактирование анкеты по ссылке'); // ------------------------------------------------

$I->amOnPage('/user/update/53');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$smet4ikPage->checkFormSmet4ik('admin-edit');
$I->see('Статус анкеты: '. $Status);
$I->see('Анкета: '. $Visible);
$I->seeInFormFields('form[name="registrationForm"]',['User[fio]'=>$FIO,'User[email]'=>$Email,'User[phone]'=>$Phone,'User[price]'=>$Price,'User[ipap_attestat_id]'=>$IPAP]);
$smet4ikPage->checkValueinSelect(['user-city_id'=>[$City],'user-professions required'=>$ProfArray,'user-smetadocs'=>$SmetnDocsArray,'user-normbases'=>$BasesArray]);

$I->amGoingTo('Возвращаюсь в список - проверяю нижнюю ссылку'); // ------------------------------------------------

$I->click('a[class="backToIndex bottom"]');
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium

$I->see('№1 - требует проверки','div[data-id="1"]');