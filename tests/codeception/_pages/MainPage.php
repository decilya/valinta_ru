<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class MainPage extends BasePage
{
    // URL страницы
    public static $URL = '/';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $TopMenu = ['/about'=>'О портале','/request'=>'Сметная программа бесплатно!','/register'=>'Регистрация в базе','/login'=>'Войти'];
    public static $TopMenuLK = ['/about'=>'О портале','/request'=>'Сметная программа бесплатно!','/user/update/1'=>'Личный кабинет','/site/logout'=>'Выйти'];

    public static $TopMenuFindSmet4ikTexts = ['О портале', 'Сметная программа бесплатно!', 'Регистрация в базе', 'Войти'];
    public static $TopMenuFindSmet4ikTextsLK = ['О портале', 'Сметная программа бесплатно!', 'Личный кабинет', 'Выйти'];
    public static $TopMenuProperty = 'div[class="linksHolder"]';

    public static $PageTexts = 'Поиск специалистов для разработки смет';
    public static $PageTextsProperty = 'h2';

    public static $AnketPriceText ='Стоимость работ:';
    public static $AnketExpText ='Образование и опыт работы:';
    public static $AnketProfText ='Профессиональная область:';
    public static $AnketBasesText ='Нормативные базы:';
    public static $AnketDocsText ='Сметная документация:';
    public static $AnketIPAPText ='Аттестат ИПАП:';

    public static $NoRecordsText = 'На портале не опубликовано ни одной анкеты.';

    public static $ResetFilterLink = 'Сбросить фильтр';

    public static $OpenFullText = '...читать полностью';
    public static $OpenFullLink = 'span[class="showMore"]';

    public static $SeeMoreLink = '#loadMore';

    public static $OpenContactText = 'Открыть контакты';

    public static $CounterText = 'Зарегистрировано сметчиков: ';

    public static $DataContainer = 'div[class="results-block"]';
    public static $HumansTotalAttribute = 'data-results-total';
    public static $HumansOnPageAttribute = 'data-results-onpage';

    public static $Filter_Prof = 'select2-filterProfessions[]';
    public static $Filter_Docs = 'select2-filterSmetaDocs[]';
    public static $Filter_Bases = 'select2-filterNormBases[]';

    public static $SortPriceAsc = 'span[data-pricesort-val="asc"]';
    public static $SortPriceDesc = 'span[data-pricesort-val="desc"]';

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

    // Проверка всех наименований полей в анкете
    public function CheckAllFieldsText($Position)
    {
        $PositionLocator='div[data-position="' . $Position . '"]';
        $this->acceptanceTester->see(self::$AnketPriceText,$PositionLocator);
        $this->acceptanceTester->see(self::$AnketExpText,$PositionLocator);
        $this->acceptanceTester->see(self::$AnketProfText,$PositionLocator);
        $this->acceptanceTester->see(self::$AnketBasesText,$PositionLocator);
        $this->acceptanceTester->see(self::$AnketDocsText,$PositionLocator);
        $this->acceptanceTester->see(self::$AnketIPAPText,$PositionLocator);
    }

    public function SeeHuman(array $Data, $Position)
    {
        foreach ($Data as $value) {
            $this->acceptanceTester->see($value, 'div[data-position="' . $Position . '"]');
        }
    }

    public function DoNotSeeHuman(array $Data)
    {
        foreach ($Data as $value) {
            $this->acceptanceTester->dontSee($value);
        }
    }

    public function SeeContact($Phone,$Email,$Position)
    {
        $this->acceptanceTester->click(self::$OpenContactText,'div[data-position="' . $Position . '"]');
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1.5); } // only for selenium
        $this->acceptanceTester->see($Phone, 'div[data-position="' . $Position . '"]');
        $this->acceptanceTester->see($Email, 'div[data-position="' . $Position . '"]');
    }

    public function SeeFullExperience($FullExperience, $humanPosition)
    {
        $this->acceptanceTester->click(MainPage::$OpenFullLink, 'div[data-position="' . $humanPosition . '"]');
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        $this->acceptanceTester->see($FullExperience, 'div[data-position="' . $humanPosition . '"]');
    }

/*    public function SeeContacts(array $Contacts, $humanPosition)
    {
        $this->acceptanceTester->click(self::$OpenContactText, 'div[data-position="' . $humanPosition . '"]');
        foreach ($Contacts as $value) {
            if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
            $this->acceptanceTester->see($value, 'div[data-position="' . $humanPosition . '"]');
        }
    }*/

    private function GetNumberPages()
    {
        $HumansTotal=$this->acceptanceTester->grabAttributeFrom(self::$DataContainer,self::$HumansTotalAttribute);
        $HumansOnPage=$this->acceptanceTester->grabAttributeFrom(self::$DataContainer,self::$HumansOnPageAttribute);
        return floor($HumansTotal/$HumansOnPage);
    }

    public function LoadNextPage()
    {
        $this->acceptanceTester->click(self::$SeeMoreLink);
    }

    public function LoadAllPages()
    {
        $Pages=self::GetNumberPages();
        for ($i=1; $i<=$Pages; $i++)
        {
            self::LoadNextPage();
            if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); }
        }
    }

    public function ApplyCityFilter($City)
    {
        $this->acceptanceTester->click('span[id="select2-user-city-container"]');
        $this->acceptanceTester->fillField('.select2-search--dropdown input[class="select2-search__field"]',$City);
        $this->acceptanceTester->click('#select2-user-city-results li[class="select2-results__option select2-results__option--highlighted"]');
    }

    public function ApplyBasesFilter(array $Bases)
    {
        foreach ($Bases as $value) {
            $this->acceptanceTester->fillField('select[name="'. self::$Filter_Bases .'"] + span ul li input[type="search"]', $value);
            $this->acceptanceTester->pressKey('select[name="'. self::$Filter_Bases .'"] + span ul li input[class="select2-search__field"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
        }
    }

    public function ApplyProfsFilter(array $Bases)
    {
        foreach ($Bases as $value) {
            $this->acceptanceTester->fillField('select[name="'. self::$Filter_Prof .'"] + span ul li input[type="search"]', $value);
            $this->acceptanceTester->pressKey('select[name="'. self::$Filter_Prof .'"] + span ul li input[class="select2-search__field"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
        }
    }

    public function ApplyDocsFilter(array $Docs)
    {
        foreach ($Docs as $value) {
            $this->acceptanceTester->fillField('select[name="'. self::$Filter_Docs .'"] + span ul li input[type="search"]', $value);
            $this->acceptanceTester->pressKey('select[name="'. self::$Filter_Docs .'"] + span ul li input[class="select2-search__field"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
        }
    }

    public function ClearCityFilter()
    {
        $this->acceptanceTester->click('#user-city + span span[class="select2-selection__clear"]');
    }

    public function ClearBasesFilter(array $Bases)
    {
        foreach ($Bases as $value) {
            $this->acceptanceTester->click('select[name="'. self::$Filter_Bases .'"] + span ul li[title="' . $value . '"] span[class="select2-selection__choice__remove"]');
        }
    }

    public function ClearProfsFilter(array $Profs)
    {
        foreach ($Profs as $value) {
            $this->acceptanceTester->click('select[name="'. self::$Filter_Prof .'"] + span ul li[title="' . $value . '"] span[class="select2-selection__choice__remove"]');
        }
    }

    public function ClearDocsFilter(array $Docs)
    {
        foreach ($Docs as $value) {
            $this->acceptanceTester->click('select[name="'. self::$Filter_Docs .'"] + span ul li[title="' . $value . '"] span[class="select2-selection__choice__remove"]');
        }
    }

    public function ClickResetFilter()
    {
        $this->acceptanceTester->click(self::$ResetFilterLink);
        $this->acceptanceTester->seeInField('select[name="'. self::$Filter_Docs .'"]','');
        $this->acceptanceTester->seeInField('select[name="'. self::$Filter_Prof .'"]','');
        $this->acceptanceTester->seeInField('select[name="'. self::$Filter_Bases .'"]','');
    }

    public function ClickSortPriceAsc()
    {
        $this->acceptanceTester->click(self::$SortPriceAsc);
    }

    public function ClickSortPriceDesc()
    {
        $this->acceptanceTester->click(self::$SortPriceDesc);
    }

}
