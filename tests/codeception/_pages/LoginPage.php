<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class LoginPage extends BasePage
{
    // URL страницы
//    public $route = '/login';
    public static $URL = '/login';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $PageTexts = 'Вход в кабинет сметчика';
    public static $PageTextsProperty = 'h1';

    public static $FormName = '#login-form';
    public static $usernameField = 'Auth[login]';
    public static $passwordField = 'Auth[password]';
    public static $formSubmitButton = 'ВОЙТИ';

    public static $EmptyFieldWarning = 'Это обязательное поле';
    public static $WrongDataWarning = 'Неправильное имя или пароль';

    public static $TopMenu = ['/about'=>'О портале','/request'=>'Сметная программа бесплатно!','/login'=>'Войти'];
    public static $TopMenuProperty = 'div[class="linksHolder"]';

    public static $FormRegisterTexts = ['Логин','Пароль','ВОЙТИ','Восстановить пароль'];

    public static $RecoverPasswordLink = 'Восстановить пароль';

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

    public function Login($username, $password)
    {
        $this->acceptanceTester->fillField(self::$usernameField, $username);
        $this->acceptanceTester->fillField(self::$passwordField, $password);
        $this->acceptanceTester->click(self::$formSubmitButton);
    }

    public function FastCheckPage()
    {
        $this->acceptanceTester->see(self::$PageTexts,self::$PageTextsProperty);
    }

    public function FullCheckPage()
    {
        self::FastCheckPage();
        foreach (self::$FormRegisterTexts as $Text) {
            $this->acceptanceTester->see($Text);
        }
    }

    public function CheckMenu()
    {
        foreach (self::$TopMenu as $Link => $Text) {
            $GrabText=$this->acceptanceTester->grabTextFrom(self::$TopMenuProperty.' a[href="'.$Link.'"]');
            if ($GrabText != $Text) $this->acceptanceTester->see('Ссылка - "'.$Text.'" не верная');
        }
    }

    public function CheckDefaultFormState()
    {
        $this->acceptanceTester->seeInFormFields(self::$FormName,[self::$usernameField=>'',self::$passwordField=>'']);
    }

    public function PasswordReset()
    {
        $this->acceptanceTester->click(self::$RecoverPasswordLink);
    }


    /*

        public function clearFilter($FilterID, $FilterText, $FilterStatus='все')
        {
            if (!empty($FilterID))
            {
                $this->actor->pressKey('input[name="searchId"]',array('ctrl', 'a'), \Facebook\WebDriver\WebDriverKeys::DELETE);
                $this->actor->pressKey('input[name="searchId"]',\Facebook\WebDriver\WebDriverKeys::ENTER);
                if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); } // only for selenium
            }
            if (!empty($FilterText))
            {
                $this->actor->pressKey('input[name="searchText"]',array('ctrl', 'a'), \Facebook\WebDriver\WebDriverKeys::DELETE);
                $this->actor->pressKey('input[name="searchId"]',\Facebook\WebDriver\WebDriverKeys::ENTER);
                if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); } // only for selenium
            }
            if ($FilterStatus!='все')
            {
                $this->actor->selectOption('searchStatus','все');
                if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); } // only for selenium
            }
        }
    */
}
