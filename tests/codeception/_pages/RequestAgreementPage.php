<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RequestAgreementPage extends BasePage
{
    // URL страницы
    public static $URL = '/request-agreement';
    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $PageTexts = 'Условия предоставления ПП SmetaWIZARD';
    public static $PageTextsProperty = 'h1';

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
        $this->acceptanceTester->see(self::$PageTexts,self::$PageTextsProperty);
    }
}
