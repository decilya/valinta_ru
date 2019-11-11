<?php

namespace tests\codeception\_pages;

//use yii\codeception\BasePage;
//use \Facebook\WebDriver\Remote\RemoteWebDriver;


/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AdminSmet4ikListPage extends \Codeception\Module
{
    // URL страницы
    public static $URL = '/user/index';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $TopMenuTexts = ['Сметчики','Заявки','Выйти (admin)'];
    public static $TopMenuProperty = 'ul[class="navbar-nav navbar-right nav"]';

    public static $PageTexts = 'Анкеты';
    public static $PageTextsProperty = 'h1';

    public static $EditSmet4ikLink = 'Редактировать';

    public static $NextPageLink = '»';
    public static $PreviousPageLink = '«';
    public static $PaginatorProperty = 'div[class="paginator"]';

    public static $FilterIDField = 'input[name="searchId"]';
    public static $FilterTextField = 'input[name="searchText"]';
    public static $FilterStatusField = 'select[name="searchUserStatus"]';
    public static $FilterFail = 'Ничего не найдено.';

    public static $Smet4ikRejectLink = 'Отклонить';
    public static $Smet4ikRejectButton = 'Отклонить';
    public static $Smet4ikCancelRejectButton = 'Отмена';

    public static $Smet4ikConfirmLink = 'Подтвердить';
    public static $Smet4ikConfirmButton = 'Подтвердить';
    public static $Smet4ikCancelConfirmButton = 'Отмена';

    public static $CheckStatus='требует проверки';
    public static $ConfStatus='подтверждена';
    public static $RejectStatus='отклонена';


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

    public function CheckMenu()
    {
        foreach (self::$TopMenuTexts as $value) {
            $this->acceptanceTester->see($value,self::$TopMenuProperty);
        }
    }

    public function FillIDFilter($ID)
    {
        $this->acceptanceTester->fillField(self::$FilterIDField,$ID);
        $this->acceptanceTester->pressKey(self::$FilterIDField,\Facebook\WebDriver\WebDriverKeys::ENTER);
    }

    public function FillTextFilter($FilterData)
    {
        $this->acceptanceTester->fillField(self::$FilterTextField,$FilterData);
        $this->acceptanceTester->pressKey(self::$FilterTextField,\Facebook\WebDriver\WebDriverKeys::ENTER);
    }

    public function SeeHuman(array $humanData, $humanID)
    {
        foreach ($humanData as $value) {
            $this->acceptanceTester->see($value,'div[data-id="' . $humanID . '"]');
        }
    }

    public function Reject($ID,$Reason,$SaveOrCacel=true)
    {
        $this->acceptanceTester->click(self::$Smet4ikRejectLink, 'div[data-id="' . $ID . '"]');
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        $this->acceptanceTester->see('Укажите причину отклонения анкеты №' . $ID . ' и нажмите «Отклонить»!');
        if ($Reason !== '') $this->acceptanceTester->fillField('#modalTextarea',$Reason);
        $Button = ($SaveOrCacel === true) ? self::$Smet4ikRejectButton : self::$Smet4ikCancelRejectButton;
        $this->acceptanceTester->click($Button, 'div[class="modal-footer"]');
    }

    public function Confirm($ID,$SaveOrCacel=true)
    {
        $this->acceptanceTester->click(self::$Smet4ikConfirmLink, 'div[data-id="' . $ID . '"]');
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        $this->acceptanceTester->see('Вы действительно хотите подтвердить правильность заполнения профиля заказчика' . $ID);
        $Button = ($SaveOrCacel === true) ? self::$Smet4ikConfirmButton : self::$Smet4ikCancelConfirmButton;
        $this->acceptanceTester->click($Button, 'div[class="modal-footer"]');
    }

    public function EditSmet4ik($ID)
    {
        $this->acceptanceTester->click(self::$EditSmet4ikLink,'div[data-id="' . $ID . '"]');
    }

    public function GetLastPage()
    {
        return $this->acceptanceTester->grabTextFrom('ul.pagination li[class="last"]');
    }

    public function NextPage()
    {
        $this->acceptanceTester->click(self::$NextPageLink, 'div[class="paginator"]');
    }

    public function PreviousPage()
    {
        $this->acceptanceTester->click(self::$PreviousPageLink, 'div[class="paginator"]');
    }

    public function GrabStatus()
    {
        return preg_replace('/№[0-9]+\ -\ /','',$this->acceptanceTester->grabMultiple('.itemBlock .itemId'));
    }

    public function GrabEditDate()
    {
        return str_replace('Изменена: ','',$this->acceptanceTester->grabMultiple('.itemBlock .itemChanged'));
    }
}
