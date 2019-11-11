<?php

// @group parallel
// @group id110

use tests\codeception\_pages\LoginPage;
use tests\codeception\_pages\AdminSmet4ikListPage;

/* @var $scenario Codeception\Scenario */

$I = new AcceptanceTester($scenario);
$I->wantTo('Проверить сортировку анкет сметчиков по умолчанию в панели управления');

$I->amGoingTo('Открываю страницу авторизации'); // ------------------------------------------------
$I->amOnPage(LoginPage::$URL);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
LoginPage::of($I)->FastCheckPage();

$I->amGoingTo('Вхожу в панель управления'); // ------------------------------------------------
$username='admin';
$password='1qwe2qaz';
LoginPage::of($I)->Login($username,$password);
if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
AdminSmet4ikListPage::of($I)->FastCheckPage();

$CountPages=AdminSmet4ikListPage::of($I)->GetLastPage();

$I->amGoingTo('Граблю со страниц статусы и даты изменения'); // ------------------------------------------------
$Status=[];
$EditDate=[];
for($i=1;$i<$CountPages;$i++)
{
    $Status=$Status+AdminSmet4ikListPage::of($I)->GrabStatus();
    $EditDate=$EditDate+AdminSmet4ikListPage::of($I)->GrabEditDate();
    AdminSmet4ikListPage::of($I)->NextPage();
//    if (method_exists($I, 'wait')) { $I->wait(1); } // only for selenium
}
$Status=$Status+AdminSmet4ikListPage::of($I)->GrabStatus();
$EditDate=$EditDate+AdminSmet4ikListPage::of($I)->GrabEditDate();

// Разделяю даты по статусым
$EditDateCheck=[];
$EditDateConf=[];
$EditDateReject=[];
for ($i=0; $i<count($Status);$i++) {
    switch ($Status[$i]){
        case AdminSmet4ikListPage::$CheckStatus:
            array_push($EditDateCheck,$EditDate[$i]);
            break;
        case AdminSmet4ikListPage::$ConfStatus:
            array_push($EditDateConf,$EditDate[$i]);
            break;
        case AdminSmet4ikListPage::$RejectStatus:
            array_push($EditDateReject,$EditDate[$i]);
            break;
    }
}

// Проверяю сортировку по статусам
$Status=str_replace(AdminSmet4ikListPage::$CheckStatus,'1',$Status);
$Status=str_replace(AdminSmet4ikListPage::$RejectStatus,'2',$Status);
$Status=str_replace(AdminSmet4ikListPage::$ConfStatus,'3',$Status);
for ($i=0; $i<count($Status)-1;$i++) {
    if ($Status[$i]>$Status[$i+1]) $I->see('Вижу что сортировка по статусу не верна в строке '.$i);
}

// Проверяю сортировку по датам внутри статусов
for ($i=0; $i<count($EditDateCheck)-1;$i++) {
    if (strtotime($EditDateCheck[$i])>strtotime($EditDateCheck[$i+1])) $I->see('Вижу что сортировка для требующих проверки не верна: "'.$EditDateCheck[$i].'" > "'.$EditDateCheck[$i+1].'"');
}
for ($i=0; $i<count($EditDateConf)-1;$i++) {
    if (strtotime($EditDateConf[$i])>strtotime($EditDateConf[$i+1])) $I->see('Вижу что сортировка для подтвержденных не верна: "'.$EditDateConf[$i].'" > "'.$EditDateConf[$i+1].'"');
}
for ($i=0; $i<count($EditDateReject)-1;$i++) {
    if (strtotime($EditDateReject[$i])>strtotime($EditDateReject[$i+1])) $I->see('Вижу что сортировка для отклоненных не верна: "'.$EditDateReject[$i].'" > "'.$EditDateReject[$i+1].'"');
}
