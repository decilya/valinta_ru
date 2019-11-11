<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ConfirmNewPasswordPage extends BasePage
{
    // URL страницы
    public static $URL = '/change-pass/';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $PageTexts = 'Смена пароля';
    public static $PageTextsProperty = 'h1';

    public static $passwordField = 'Auth[pass_change]';
    public static $passwordRepeatField = 'Auth[pass_change_repeat]';
    public static $formSubmitButton = 'СМЕНИТЬ ПАРОЛЬ';

    public static $HelpText = '(не менее 6 символов, включая одну заглавную букву и одну цифру)';

    public static $EmptyFieldWarning = 'Это обязательное поле';
    public static $DigitWarning = 'Пароль должен содержать хотя бы одну цифру';
    public static $UpLetterWarning = 'Пароль должен содержать хотя бы одну заглавную букву';
    public static $ShortPasswordWarning = 'Пароль должен быть не менее 6 символов';
    public static $LongPasswordWarning = 'Пароль должен быть не более 255 символов';
    public static $RepeatWarning = 'Повторите пароль для подтверждения';
    public static $SuccessWarning = 'Поздравляем! Вы успешно сменили пароль.';
    public static $ErrorWarning = 'Произошла ошибка при смене пароля.';

    public static $TopMenuTexts = ['О портале', 'Сметная программа бесплатно!', 'Войти'];
    public static $TopMenuProperty = 'div[class="linksHolder"]';

    public static $CloseMessageButton = 'ЗАКРЫТЬ';

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

    public function SetNewPassword($password, $passwordrepeat)
    {
        $this->acceptanceTester->fillField(self::$passwordField, $password);
        $this->acceptanceTester->fillField(self::$passwordRepeatField, $passwordrepeat);
        $this->acceptanceTester->click(self::$formSubmitButton);
    }

    public function FastCheckPage()
    {
        $this->acceptanceTester->see(self::$PageTexts, self::$PageTextsProperty);
    }

    public function CheckMenu()
    {
        foreach (self::$TopMenuTexts as $value) {
            $this->acceptanceTester->see($value, self::$TopMenuProperty);
        }
    }

    public function CloseSuccessMessage()
    {
        $this->acceptanceTester->click(self::$CloseMessageButton);
    }
}
