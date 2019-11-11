<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class RegisterPage extends BasePage
{
    // URL страницы
    public static $URL = '/register';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $TopMenu = ['/about'=>'О портале','/request'=>'Сметная программа бесплатно!','/'=>'Поиск сметчиков','/login'=>'Войти'];
    public static $TopMenuProperty = 'div[class="linksHolder"]';

    public static $PageTexts = 'Регистрация в базе сметчиков';
    public static $PageTextsProperty = 'h2';

    public static $FormTexts = ['Ф.И.О.','Адрес электронной почты','Пароль (должен содержать не менее 6 символов, включая одну заглавную букву и одну цифру)','Подтверждение пароля','Мобильный телефон','Город','Номер профессионального аттестата ИПАП (если есть)','Профессиональная область','Сметная документация','Нормативные базы','Образование и опыт работы','Стоимость работ (руб.)','Я согласен с правилами сервиса','Регистрация'];

    public static $FormRegisterName = 'form[name="registrationForm"]';
    public static $FormRegisterField_FIO = 'User[fio]';
    public static $FormRegisterField_Email = 'User[email]';
    public static $FormRegisterField_Pass = 'User[password]';
    public static $FormRegisterField_PassRep = 'User[password_repeat]';
    public static $FormRegisterField_Phone = 'User[phone]';
    public static $FormRegisterField_IPAP = 'User[ipap_attestat_id]';
    public static $FormRegisterField_City = 'User[city_id]';
    public static $FormRegisterField_Prof = 'User[professions][]';
    public static $FormRegisterField_Prof_ID = '#user-professions';
    public static $FormRegisterField_Docs = 'User[smetaDocs][]';
    public static $FormRegisterField_Docs_ID = '#user-smetadocs';
    public static $FormRegisterField_Bases = 'User[normBases][]';
    public static $FormRegisterField_Bases_ID = '#user-normbases';
    public static $FormRegisterField_Exp = 'User[experience]';
    public static $FormRegisterField_Price = 'User[price]';
    public static $FormRegisterField_Agreement = 'User[user_agreement]';
    public static $FormRegisterField_Agreement_id = '#user-user_agreement';
    public static $FormRegisterButton = 'Регистрация';
    public static $FormRegisterSuccess = 'Вы успешно зарегистрировались на сайте бесплатных объявлений для специалистов по сметному делу «Valinta.ru»!';

    public static $FormRequiredAgreementLinkText ='правилами сервиса';

    public static $FormRequiredFieldWarn = 'Это обязательное поле';
    public static $FormRegisterAgreementWarn = 'Необходимо согласиться с правилами сервиса';

    public static $FormRegisterEmailWrongWarn = 'Некорректный адрес';
    public static $FormRegisterEmailLongWarn = 'Поле должно содержать не более 129 символов';
    public static $FormRegisterEmailExistWarn = 'E-mail уже зарегистрирован';

    public static $FormRegisterPhoneWrongWarn = 'Пожалуйста, введите номер мобильного телефона';

    public static $FormRegisterIPAPLongWarn = 'Поле должно содержать не более 15 символов';

    public static $FormRegisterFIOLongWarn = 'Поле должно содержать не более 100 символов';

    public static $FormRegisterPriceWrongWarn = 'Введите число от 0 до 9999999';
    public static $FormRegisterPriceNoDigWarn = 'Пожалуйста, введите стоимость в цифрах без пробелов (от 0 до 9999999)';

    public static $FormRegisterPassDigWarn = 'Пароль должен содержать хотя бы одну цифру';
    public static $FormRegisterPassCapsWarn = 'Пароль должен содержать хотя бы одну заглавную букву';
    public static $FormRegisterPassShortWarn = 'Пароль должен быть не менее 6 символов';
    public static $FormRegisterPassLongWarn = 'Пароль должен быть не более 255 символов';
    public static $FormRegisterPassRepeatWarn = 'Повторите пароль для подтверждения';

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
            $this->acceptanceTester->see($Text,self::$FormRegisterName);
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
        $this->acceptanceTester->seeInFormFields(self::$FormRegisterName,[self::$FormRegisterField_FIO=>'',self::$FormRegisterField_Email=>'',self::$FormRegisterField_Pass=>'',self::$FormRegisterField_PassRep=>'',self::$FormRegisterField_Phone=>'',self::$FormRegisterField_City=>'',self::$FormRegisterField_IPAP=>'',self::$FormRegisterField_Prof=>'',self::$FormRegisterField_Docs=>'',self::$FormRegisterField_Bases=>'',self::$FormRegisterField_Exp=>'',self::$FormRegisterField_Price=>'',self::$FormRegisterField_Agreement=>false]);
    }

    // false - если не хотим менять значение текстовое поле
    // [] - если не хотим менять значение селект
    public function Register($FIO,$Email,$Pass,$PassRep,$Phone,$City,array $Profs,array $Docs,array $Bases,$IPAP,$Price,$Exp,$Agreement)
    {
        if ($FIO !== false) $this->acceptanceTester->fillField(self::$FormRegisterField_FIO, $FIO);
        if ($Email !== false) $this->acceptanceTester->fillField(self::$FormRegisterField_Email, $Email);
        if ($Pass !== false) $this->acceptanceTester->fillField(self::$FormRegisterField_Pass, $Pass);
        if ($PassRep !== false) $this->acceptanceTester->fillField(self::$FormRegisterField_PassRep, $PassRep);
        if ($Phone !== false) $this->acceptanceTester->fillField(self::$FormRegisterField_Phone, $Phone);
        if ($City !== false) self::FillCity($City);
        if (!empty($Profs)) self::FillSelect(self::$FormRegisterField_Prof_ID,$Profs);
        if (!empty($Docs)) self::FillSelect(self::$FormRegisterField_Docs_ID,$Docs);
        if (!empty($Bases)) self::FillSelect(self::$FormRegisterField_Bases_ID,$Bases);
        if ($IPAP !== false) $this->acceptanceTester->fillField(self::$FormRegisterField_IPAP, $IPAP);
        if ($Price !== false) $this->acceptanceTester->fillField(self::$FormRegisterField_Price, $Price);
        if ($Exp !== false) $this->acceptanceTester->fillField(self::$FormRegisterField_Exp, $Exp);
        if ($Agreement !== false) $this->acceptanceTester->click(self::$FormRegisterField_Agreement_id);
        $this->acceptanceTester->click(self::$FormRegisterButton,self::$FormRegisterName);
    }

    // Заполнение селектов для формы - кроме города
    private function FillSelect($Field,array $Data)
    {
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        foreach ($Data as $value) {
            $this->acceptanceTester->fillField($Field . ' + span ul li input[type="search"]', $value);
            $this->acceptanceTester->pressKey($Field . ' + span ul li input[class="select2-search__field"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
        }
    }

    // Заполнение города
    private function FillCity($City)
    {
        $this->acceptanceTester->click('span[id="select2-user-city_id-container"]');
        $this->acceptanceTester->pressKey('.select2-search--dropdown input[class="select2-search__field"]',$City);
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        $this->acceptanceTester->click('#select2-user-city_id-results li[class="select2-results__option select2-results__option--highlighted"]');
    }

/*
    public function submittobase(array $humanData, $humanCity, array $humanSelect)
    {
        foreach ($humanData as $field => $value) {
            $this->actor->fillField('[name="User[' . $field . ']"]', $value);
        }
        if(!empty($humanCity)) {
            $this->actor->click('span[id="select2-user-city_id-container"]');
//            $this->actor->fillField('.select2-search--dropdown input[class="select2-search__field"]',$humanCity);
            $this->actor->pressKey('.select2-search--dropdown input[class="select2-search__field"]',$humanCity);
            if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); } // only for selenium
            $this->actor->click('#select2-user-city_id-results li[class="select2-results__option select2-results__option--highlighted"]');
        }
        if(!empty($humanSelect)) {
        if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); }} // only for selenium
        foreach ($humanSelect as $field => $array) {
            foreach ($array as $value) {
                $this->actor->fillField('#' . $field . ' + span ul li input[type="search"]', $value);
                if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); } // only for selenium
                $this->actor->click('#select2-' . $field . '-results li[class="select2-results__option select2-results__option--highlighted"]');
            }
        }
        if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); } // only for selenium
        $this->actor->click('form[name="registrationForm"] button[type="submit"]');
    }


    public function removefromselect2(array $humanSelect, $clearCity)
    {
        foreach ($humanSelect as $field => $array) {
            foreach ($array as $value) {
                $this->actor->click('#' . $field . ' + span ul li[title="' . $value . '"] span[class="select2-selection__choice__remove"]');
                if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); }
            }
        }
        if ($clearCity == "clear_city") {
            $this->actor->click('#user-city_id + span span[class="select2-selection__clear"]');
        }
    }

/*    public function checkFormProg()
    {
        $this->actor->see('Сметная программа бесплатно!');
        $this->actor->see('Вы можете получить бесплатный доступ');
        $this->actor->see('к программе SmetaWIZARD.');
        $this->actor->see('На 7 дней. С любой сметной базой.');
        $this->actor->see('Оставте Вашу заявку и менеджер');
        $this->actor->see('свяжется с Вами в ближайшее время:');
        $this->actor->see('ПН-ПТ с 9.00 до 18.00 МСК.');
        $this->actor->see('Ф.И.О.', 'form[name="requestForm"]');
        $this->actor->see('Адрес электронной почты', 'form[name="requestForm"]');
        $this->actor->see('Мобильный телефон','form[name="requestForm"]');
        $result = $this->actor->grabAttributeFrom('form[name="requestForm"] button[class="btn btn-success btn-lg"]', 'disabled');
        if ($result == true) {
            $this->actor->See('Что кнопка на заявку заблокирована');
        }
    }*/

// типы проверок:
// smet4ik-submit - создание
// smet4ik-edit - редактирование в ЛК
// admin-edit - редактирование в СУ
/*    public function checkFormSmet4ik($check_type)
    {
        if($check_type == 'smet4ik-submit') {
            $this->actor->see('Регистрация в базе сметчиков');
        } else {
            $this->actor->see('Личный кабинет');
        }
        $this->actor->see('Ф.И.О.','form[name="registrationForm"]');
        $this->actor->see('Адрес электронной почты','form[name="registrationForm"]');
        if($check_type == 'smet4ik-submit') {
            $this->actor->see('Пароль (должен содержать не менее 6 символов, включая одну заглавную букву и одну цифру)','form[name="registrationForm"]');
            $this->actor->see('Подтверждение пароля','form[name="registrationForm"]');
        }
        $this->actor->see('Мобильный телефон','form[name="registrationForm"]');
        $this->actor->see('Город','form[name="registrationForm"]');
        if($check_type == 'admin-edit') {
            $this->actor->see('Номер профессионального аттестата ИПАП', 'form[name="registrationForm"]');
        } else {
            $this->actor->see('Номер профессионального аттестата ИПАП (если есть)', 'form[name="registrationForm"]');
        }
        $this->actor->see('Профессиональная область','form[name="registrationForm"]');
        $this->actor->see('Сметная документация','form[name="registrationForm"]');
        $this->actor->see('Нормативные базы','form[name="registrationForm"]');
        $this->actor->see('Опыт работы','form[name="registrationForm"]');
        $this->actor->see('Стоимость работ','form[name="registrationForm"]');
        $result = $this->actor->grabAttributeFrom('form[name="registrationForm"] button', 'disabled');
        if ( $result == true ) { $this->actor->See('Что кнопка на регитсрацию заблокирована'); }
    }*/

/*    public function checkValueinSelect(array $humanSelect)
    {
        foreach ($humanSelect as $field => $array) {
            foreach ($array as $value) {
                if($field == 'user-city_id')
                {
                    $this->actor->see($value,'form[name="registrationForm"] #select2-' . $field . '-container');
                }else{
                    $this->actor->see($value,'form[name="registrationForm"] div[class="form-group field-' . $field . '"]');
                }
            }
        }

    }*/

}
