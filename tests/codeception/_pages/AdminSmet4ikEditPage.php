<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AdminSmet4ikEditPage extends BasePage
{
    // URL страницы
    public static $URL = '/user/update/';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $TopMenuTexts = ['Сметчики','Заявки','Выйти (admin)'];
    public static $TopMenuProperty = 'ul[class="navbar-nav navbar-right nav"]';
    public static $PageTexts = ['Статус анкеты:','Отправить ссылку для смены пароля','Вернуться к списку'];

    public static $NamePersonalID = 'Личный кабинет № ';

    public static $FormTexts = ['Ф.И.О.','Адрес электронной почты','Мобильный телефон','Город','Номер профессионального аттестата ИПАП','Профессиональная область','Сметная документация','Нормативные базы','Образование и опыт работы','Стоимость работ (руб.)','Сохранить'];
    public static $FormName = 'form[name="registrationForm"]';
    public static $FormField_FIO = 'User[fio]';
    public static $FormField_Email = 'User[email]';
    public static $FormField_Phone = 'User[phone]';
    public static $FormField_City = 'User[city_id]';
    public static $FormField_City_ID = '#user-city_id';
    public static $FormField_IPAP = 'User[ipap_attestat_id]';
    public static $FormField_Prof = 'User[professions][]';
    public static $FormField_Prof_ID = '#user-professions';
    public static $FormField_Docs = 'User[smetaDocs][]';
    public static $FormField_Docs_ID = '#user-smetadocs';
    public static $FormField_Bases = 'User[normBases][]';
    public static $FormField_Bases_ID = '#user-normbases';
    public static $FormField_Exp = 'User[experience]';
    public static $FormField_Price = 'User[price]';

    public static $FormButton = 'Сохранить';
    public static $FormSuccess = 'Вы успешно обновили анкету.';

    public static $FormRequiredFieldWarn = 'Это обязательное поле';

    public static $FormEmailWrongWarn = 'Некорректный адрес';
    public static $FormEmailLongWarn = 'Поле должно содержать не более 129 символов';
    public static $FormEmailExistWarn = 'E-mail уже зарегистрирован';

    public static $FormPhoneWrongWarn = 'Пожалуйста, введите номер мобильного телефона';

    public static $FormIPAPLongWarn = 'Поле должно содержать не более 15 символов';

    public static $FormFIOLongWarn = 'Поле должно содержать не более 100 символов';

    public static $FormPriceWrongWarn = 'Введите число от 0 до 9999999';
    public static $FormPriceNoDigWarn = 'Пожалуйста, введите стоимость в цифрах без пробелов (от 0 до 9999999)';

    public static $HideSmet4ikLink = 'Скрыть анкету';
    public static $HideSmet4ikWarn = 'Вы скрыли анкету, она убрана из результатов поиска.';

    public static $UnHideSmet4ikLink = 'Показать анкету';
    public static $UnHideSmet4ikWarn = 'Вы показали анкету, если анкета подтверждена, она появится в результатах поиска.';

    public static $CheckIPAPLink = 'проверить подлинность';

    public static $ResetPasswordLink = 'Отправить ссылку для смены пароля';

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

    public function FastCheckPage($ID)
    {
        $this->acceptanceTester->see(self::$NamePersonalID . $ID);
    }

    public function FullCheckPage($ID)
    {
        self::FastCheckPage($ID);
        foreach (self::$FormTexts as $Text) {
            $this->acceptanceTester->see($Text,self::$FormName);
        }
    }

    // false - если не хотим проверять значение текстовое поле
    // [] - если не хотим проверять значение селект
    public function CheckDefaultFormState($FIO,$Email,$Phone,$City,array $Profs,array $Docs,array $Bases,$IPAP,$Price,$Exp)
    {
        $aSelectValue = $this->acceptanceTester->grabMultiple(self::$FormName .' select[name="'. self::$FormField_City .'"] option[selected="selected"]','data-text');
        if (!empty($City)) if ( ! in_array($City, $aSelectValue, true) ) { $this->acceptanceTester->see($City .' - не найдено'); }
        $aSelectValue = $this->acceptanceTester->grabMultiple(self::$FormName .' select[name="'. self::$FormField_Prof .'"] option[selected=""]','data-text');
        if (!empty($Profs)) foreach ($Profs as $value) {
            if ( ! in_array($value, $aSelectValue, true) ) { $this->acceptanceTester->see($value .' - не найдено'); }
        }
        $aSelectValue = $this->acceptanceTester->grabMultiple(self::$FormName .' select[name="'. self::$FormField_Docs .'"] option[selected=""]','data-text');
        if (!empty($Docs)) foreach ($Docs as $value) {
            if ( ! in_array($value, $aSelectValue, true) ) { $this->acceptanceTester->see($value .' - не найдено'); }
        }
        $aSelectValue = $this->acceptanceTester->grabMultiple(self::$FormName .' select[name="'. self::$FormField_Bases .'"] option[selected=""]','data-text');
        if (!empty($Bases)) foreach ($Bases as $value) {
            if ( ! in_array($value, $aSelectValue, true) ) { $this->acceptanceTester->see($value .' - не найдено'); }
        }
        $this->acceptanceTester->seeInFormFields(self::$FormName,[self::$FormField_FIO=>$FIO,self::$FormField_Email=>$Email,self::$FormField_Phone=>$Phone,self::$FormField_IPAP=>$IPAP,self::$FormField_Exp=>$Exp,self::$FormField_Price=>$Price]);
    }

    public function CheckStatus($Status,$Visible,$WhyCancel)
    {
        $this->acceptanceTester->see($Status,'div[class="statusHolder"]');
        if ($WhyCancel !== '') $this->acceptanceTester->see($WhyCancel,'div[class="statusHolder"]');
        $this->acceptanceTester->see($Visible,'div[class="statusHolder"]');
    }

    // false - если не хотим менять значение текстовое поле
    // [] - если не хотим менять значение селект
    public function Update($FIO,$Email,$Phone,$City,array $Profs,array $Docs,array $Bases,$IPAP,$Price,$Exp)
    {
        if ($FIO !== false) $this->acceptanceTester->fillField(self::$FormField_FIO, $FIO);
        if ($Email !== false) $this->acceptanceTester->fillField(self::$FormField_Email, $Email);
        if ($Phone !== false) $this->acceptanceTester->fillField(self::$FormField_Phone, $Phone);
        if ($City !== false) self::FillCity($City);
        if (!empty($Profs)) self::FillSelect(self::$FormField_Prof_ID,$Profs);
        if (!empty($Docs)) self::FillSelect(self::$FormField_Docs_ID,$Docs);
        if (!empty($Bases)) self::FillSelect(self::$FormField_Bases_ID,$Bases);
        if ($IPAP !== false) $this->acceptanceTester->fillField(self::$FormField_IPAP, $IPAP);
        if ($Price !== false) $this->acceptanceTester->fillField(self::$FormField_Price, $Price);
        if ($Exp !== false) $this->acceptanceTester->fillField(self::$FormField_Exp, $Exp);
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        $this->acceptanceTester->click(self::$FormButton,self::$FormName);
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

    // Очистка поля город
    public function ClearCity()
    {
        $this->acceptanceTester->click(self::$FormField_City_ID.' + span span[class="select2-selection__clear"]');
    }

    public function RemoveFromProfs(array $Profs)
    {
        foreach ($Profs as $value) {
            $this->acceptanceTester->click(self::$FormField_Prof_ID.' + span ul li[title="' . $value . '"] span[class="select2-selection__choice__remove"]');
        }
    }

    public function RemoveFromDocs(array $Docs)
    {
        foreach ($Docs as $value) {
            $this->acceptanceTester->click(self::$FormField_Docs_ID.' + span ul li[title="' . $value . '"] span[class="select2-selection__choice__remove"]');
        }
    }

    public function RemoveFromBases(array $Bases)
    {
        foreach ($Bases as $value) {
            $this->acceptanceTester->click(self::$FormField_Bases_ID.' + span ul li[title="' . $value . '"] span[class="select2-selection__choice__remove"]');
        }
    }

    public function Hide()
    {
        $this->acceptanceTester->click(self::$HideSmet4ikLink);
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        $this->acceptanceTester->see(self::$HideSmet4ikWarn);
        $this->acceptanceTester->see(self::$UnHideSmet4ikLink);
    }

    public function UnHide()
    {
        $this->acceptanceTester->click(self::$UnHideSmet4ikLink);
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        $this->acceptanceTester->see(self::$UnHideSmet4ikWarn);
        $this->acceptanceTester->see(self::$HideSmet4ikLink);
    }

    public function PasswordReset($EmailForMessage)
    {
        $this->acceptanceTester->click(self::$ResetPasswordLink);
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(2); } // only for selenium
        $this->acceptanceTester->see('На адрес '.$EmailForMessage.' отправлено письмо с ссылкой для смены пароля.');
    }

}
