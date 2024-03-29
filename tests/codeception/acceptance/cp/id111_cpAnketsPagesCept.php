<?php

// @group cp
// @group cp-lists
// @group id111

use tests\codeception\_pages\LoginPage;

// Исходные данные ------------------------------------------------
$RecordsInBase = 60;
// ------------------------------------------------

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить сортировку по умолчанию в панели управления');

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

for($i=1; $i<$Pages; $i++) {
    $I->click('»','div[class="paginator"]');
    if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
}

$anketID=53;
$loginPage->seeWorksheet(['№'. $anketID .' - подтверждена',
    'Изменена: 04.12.2010',
    'Анкета: показана',
    'ФИО: Шихранов Артём Якубович',
    'Тел.: +7(921)179-83-46',
    'E-mail: diuscheetur@mail.ru',
    'Город: Мичуринск (Тамбовская область)',
    'Профессиональная область: Реставрационные работы; Газоснабжение; Водоснабжение и водоотведение; Сети связи, видеонаблюдение; Архитектурные решения; Земляные работы',
    'Нормативные базы: -',
    'Сметная документация: Сводный сметный расчет/Объектная смета',
    'Номер профессионального аттестата ИПАП: 936514',
    'Стоимость от: 248622'],$anketID);

$I->amGoingTo('Последовательно перехожу на первую страницу'); // ------------------------------------------------

for($i=$Pages-1; $i>0; $i--) {
    $I->click((string)$i,'div[class="paginator"]');
    if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
}

$anketID=1;
$loginPage->seeWorksheet(['№'. $anketID .' - требует проверки',
    'Изменена: 15.01.1970',
    'Сметчик хочет: показать',
    'ФИО: Унтилова Татьяна Алексеевна',
    'E-mail: mjiquy_1991@xaker.ru',
    'Город: Барыш (Ульяновская область)',
    'Тел.: +7(921)925-26-94',
    'Профессиональная область: Другое; Сети связи, видеонаблюдение; Пусконаладочные работы; Охранно-пожарные системы; Реконструкция зданий и сооружений; Ремонтные работы по текущему и кап. ремонту; Отделочные работы; Фасадные работы',
    'Нормативные базы: Госэталон; Индивидуальные/фирменные; Ведомственные; ПНР',
    'Сметная документация: Форма КС-3; Тендерная документация; Экспертиза смет',
    'Номер профессионального аттестата ИПАП: 526044',
    'Стоимость от: 530839'],$anketID);