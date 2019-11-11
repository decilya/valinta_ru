<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class Err403Page extends BasePage
{
    // URL страницы
    public static $URL = '/xxx';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $TopMenu = ['/about'=>'О портале','/request'=>'Сметная программа бесплатно!','/login'=>'Войти'];
    public static $TopMenuProperty = 'div[class="linksHolder"]';

    public static $PageTexts = 'Страница не найдена или доступ к ней запрещен';
    public static $PageTextsProperty = 'h2';

    /**
     * @var \AcceptanceTester;
     */

    protected $acceptanceTester;

    public function __construct(\AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
    }

    public static function of(\AcceptanceTester $I)
    {
        return new static($I);
    }

    public function FastCheckPage()
    {
        $this->acceptanceTester->see(self::$PageTexts, self::$PageTextsProperty);
    }

}
