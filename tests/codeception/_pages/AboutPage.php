<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AboutPage extends BasePage
{
    // URL страницы
    public $route = '/about';

    public static $TopMenuTexts = ['Сметная программа бесплатно!','Регистрация в базе','Войти'];
    public static $TopMenuLKTexts = ['Сметная программа бесплатно!','Поиск сметчиков','Выйти'];
    public static $TopMenuProperty = 'div[class="linksHolder"]';
    public static $PageTexts = ['Портал Valinta.ru (Валинта.ру) — это актуальная база специалистов по расчету и составлению сметной документации в Санкт-Петербурге и других регионах Российской Федерации.'];

    public static $AboutLink = '/about';
    public static $AboutLinkProperty = 'class="aboutLink"';

}
