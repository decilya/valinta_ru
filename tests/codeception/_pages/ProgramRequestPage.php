<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ProgramRequestPage extends BasePage
{
    // URL страницы
    public static $URL = '/request';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $TopMenu = ['/about'=>'О портале','/register'=>'Регистрация в базе','/'=>'Поиск сметчиков','/login'=>'Войти'];
    public static $TopMenuLK = ['/about'=>'О портале','/user/update/1'=>'Личный кабинет','/'=>'Поиск сметчиков','/site/logout'=>'Выйти'];
    public static $TopMenuProperty = 'div[class="linksHolder"]';

    public static $PageTexts = "Получите трехдневный бесплатный доступ к программе SmetaWIZARD.\nС любой сметной базой.";
    public static $PageTextsProperty = 'h2';

    public static $FormTexts = ['Ф.И.О.','Адрес электронной почты','Мобильный телефон','я согласен с условиями предоставления ПП SmetaWIZARD','Отправить'];

    public static $FormGetProgramName = 'form[name="requestForm"]';
    public static $FormGetProgramField_FIO = 'Request[fio]';
    public static $FormGetProgramField_Email = 'Request[email]';
    public static $FormGetProgramField_Phone = 'Request[phone]';
    public static $FormGetProgramField_Agreement = 'Request[request_agreement]';
    public static $FormGetProgramField_Agreement_id = '#request-request_agreement';
    public static $FormGetProgramButton = 'Отправить';
    public static $FormGetProgramSuccess = 'Поздравляем! Ваша заявка успешно отправлена! Наши менеджеры с Вами свяжутся!';

    public static $FormRequiredRequestAgreementLinkText ='условиями предоставления ПП SmetaWIZARD';

    public static $FormRequiredFieldWarn = 'Это обязательное поле';
    public static $FormGetProgramAgreementWarn = 'Необходимо согласиться с условиями предоставления ПП SmetaWIZARD.';

    public static $FormGetProgramEmailWrongWarn = 'Некорректный адрес';
    public static $FormGetProgramEmailLongWarn = 'Поле должно содержать не более 129 символов';

    public static $FormGetProgramPhoneWrongWarn = 'Пожалуйста, введите номер мобильного телефона';

    public static $FormGetProgramFIOWrongWarn = 'Поле должно содержать не более 100 символов';

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

    public function FullCheckPage()
    {
        self::FastCheckPage();
        foreach (self::$FormTexts as $Text) {
            $this->acceptanceTester->see($Text,self::$FormGetProgramName);
        }
    }

    // false - если не хотим менять значение текстовое поле
    public function SendRequest($FIO,$Email,$Phone,$Agreement)
    {
        if ($FIO !== false) $this->acceptanceTester->fillField(self::$FormGetProgramField_FIO, $FIO);
        if ($Email !== false) $this->acceptanceTester->fillField(self::$FormGetProgramField_Email, $Email);
        if ($Phone !== false) $this->acceptanceTester->fillField(self::$FormGetProgramField_Phone, $Phone);
        if ($Agreement !== false) {
            if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
            $this->acceptanceTester->click(self::$FormGetProgramField_Agreement_id);
        }
        $this->acceptanceTester->click(self::$FormGetProgramButton,self::$FormGetProgramName);
    }

    public function CheckMenuSite()
    {
        self::CheckMenu(self::$TopMenu);
    }

    public function CheckMenuLK()
    {
        self::CheckMenu(self::$TopMenuLK);
    }

    private function CheckMenu($Menu)
    {
        foreach ($Menu as $Link => $Text) {
            $GrabText=$this->acceptanceTester->grabTextFrom(self::$TopMenuProperty.' a[href="'.$Link.'"]');
            if ($GrabText != $Text) $this->acceptanceTester->see('Ссылка - "'.$Text.'" не верная');
        }
    }

    public function CheckDefaultFormState($FIO,$Email,$Phone,$Agreement)
    {
        $this->acceptanceTester->seeInFormFields(self::$FormGetProgramName,[self::$FormGetProgramField_FIO=>$FIO,self::$FormGetProgramField_Email=>$Email,self::$FormGetProgramField_Phone=>$Phone,self::$FormGetProgramField_Agreement=>$Agreement]);
    }

}
