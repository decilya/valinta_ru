<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RecoverPasswordPage extends BasePage
{
    // URL страницы
    public static $URL = '/recover';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $PageTexts = 'Восстановление пароля';
    public static $PageTextsProperty = 'h1';

    public static $usernameField = '#auth-login';
    public static $formSubmitButton = 'ВОССТАНОВИТЬ';

    public static $EmptyFieldWarning = 'Это обязательное поле';
    public static $WrongDataWarning = 'Пользователь не найден';
    public static $SuccessWarning = 'На ваш e-mail отправлено письмо с ссылкой для смены пароля.';

    public static $TopMenuTexts = ['О портале','Сметная программа бесплатно!','Войти'];
    public static $TopMenuProperty = 'div[class="linksHolder"]';

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

    public function SubmitForPassword($username)
    {
        $this->acceptanceTester->fillField(self::$usernameField, $username);
        $this->acceptanceTester->click(self::$formSubmitButton);
    }

    public function FastCheckPage()
    {
        $this->acceptanceTester->see(self::$PageTexts,self::$PageTextsProperty);
    }

    public function CheckMenu()
    {
        foreach (self::$TopMenuTexts as $value) {
            $this->acceptanceTester->see($value,self::$TopMenuProperty);
        }
    }
}
