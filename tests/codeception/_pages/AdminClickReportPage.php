<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AdminClickReportPage extends BasePage
{
    // URL страницы
    public static $URL = '/report/all';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $PageTexts = 'Просмотр контактов по всем анкетам';
    public static $PageTextsProperty = 'h2';

    public static $_7daysButton = '7 дней';
    public static $_7daysIDButton = '#filterBlock button[data-range="7days"]';
    public static $_30daysButton = '30 дней';
    public static $_30daysIDButton = '#filterBlock button[data-range="30days"]';
    public static $_90daysButton = '90 дней';
    public static $_90daysIDButton = '#filterBlock button[data-range="90days"]';
    public static $_365daysButton = '365 дней';
    public static $_365daysIDButton = '#filterBlock button[data-range="365days"]';

    public static $DetailIDSelect = 'select[name="detail"]';
    public static $DetailDaysSelect = 'days';
    public static $DetailWeeksSelect = 'weeks';
    public static $DetailMonthsSelect = 'months';

    public static $DateRangeID = '#dateRangePicker';

    public static $DateFromManualID = 'input[name="daterangepicker_start"]';
    public static $DateToManualID = 'input[name="daterangepicker_end"]';
    public static $DateFromSubmitButton = 'Показать';
    public static $DateFromCancelButton = 'Отменить';

    public static $TopMenuTexts = ['Сметчики','Заявки','Отчеты','Выйти (admin)'];
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

    public function FastCheckPage()
    {
        $this->acceptanceTester->see(self::$PageTexts,self::$PageTextsProperty);
    }

// Если хотим увидеть, что не выбран не один период $SelectedRange = false
    public function CheckPageState($SelectedRange,$SelectedDetail,$DateFrom,$DateTo)
    {
        if ( $SelectedRange === false )
        {
            if ( $this->acceptanceTester->grabAttributeFrom(self::$_7daysIDButton, 'class') != 'default' )  $this->acceptanceTester->See("Период ". self::$_7daysButton ." не должен быть выбран");
            if ( $this->acceptanceTester->grabAttributeFrom(self::$_30daysIDButton, 'class') != 'default' )  $this->acceptanceTester->See("Период ". self::$_30daysButton ." не должен быть выбран");
            if ( $this->acceptanceTester->grabAttributeFrom(self::$_90daysIDButton, 'class') != 'default' )  $this->acceptanceTester->See("Период ". self::$_90daysButton ." не должен быть выбран");
            if ( $this->acceptanceTester->grabAttributeFrom(self::$_365daysIDButton, 'class') != 'default' )  $this->acceptanceTester->See("Период ". self::$_365daysButton ." не должен быть выбран");
        }else{
            if ( $this->acceptanceTester->grabAttributeFrom($SelectedRange, 'class') != 'active' )  $this->acceptanceTester->See("Не выбран период ".$SelectedRange);
        }
        if ( $this->acceptanceTester->grabAttributeFrom(self::$DetailIDSelect, 'value') != $SelectedDetail )  $this->acceptanceTester->See("Не выбрана детализация ".$SelectedDetail);
        $DateRange=$DateFrom.'  -  '.$DateTo;
        if ( $this->acceptanceTester->grabAttributeFrom(self::$DateRangeID, 'value') != $DateRange)  $this->acceptanceTester->See("Выбран неверный диапозон дат ".$DateRange);
    }

    public function CheckMenu()
    {
        foreach (self::$TopMenuTexts as $value) {
            $this->acceptanceTester->see($value,self::$TopMenuProperty);
        }
    }

    public function Click7Days()
    {
        $this->acceptanceTester->click(self::$_7daysButton);
    }

    public function Click30Days()
    {
        $this->acceptanceTester->click(self::$_30daysButton);
    }

    public function Click90Days()
    {
        $this->acceptanceTester->click(self::$_90daysButton);
    }

    public function Click365Days()
    {
        $this->acceptanceTester->click(self::$_365daysButton);
    }

    public function SelectDays()
    {
        $this->acceptanceTester->selectOption(self::$DetailIDSelect,self::$DetailDaysSelect);
    }

    public function SelectWeeks()
    {
        $this->acceptanceTester->selectOption(self::$DetailIDSelect,self::$DetailWeeksSelect);
    }

    public function SelectMonths()
    {
        $this->acceptanceTester->selectOption(self::$DetailIDSelect,self::$DetailMonthsSelect);
    }

    public function SetManualPeriod($DateFrom,$DateTo)
    {
        $this->acceptanceTester->click(self::$DateRangeID);
        $this->acceptanceTester->fillField(self::$DateFromManualID,$DateFrom);
        $this->acceptanceTester->fillField(self::$DateToManualID,$DateTo);
        $this->acceptanceTester->click(self::$DateFromSubmitButton);
    }

    public function CheckPeriodsINSelect(array $PeriodsListVisible,array $PeriodsListHidden)
    {
        foreach ($PeriodsListVisible as $value) {
            if ($this->acceptanceTester->grabAttributeFrom('select option[value="'.$value.'"]','class') == 'hidden' ) $this->acceptanceTester->see('Не должен видеть детализацию по '.$value);
        }
        foreach ($PeriodsListHidden as $value) {
            if ($this->acceptanceTester->grabAttributeFrom('select option[value="'.$value.'"]','class') != 'hidden' ) $this->acceptanceTester->see('Должен видеть детализацию по '.$value);
        }
    }

}
