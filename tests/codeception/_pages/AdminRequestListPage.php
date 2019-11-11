<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AdminRequestListPage extends BasePage
{
    // URL страницы
    public static $URL = '/request/index';

    // Эти свойства определяют отображение пользовательского интерфейса для страницы регистрации
    public static $TopMenuTexts = ['Сметчики','Заявки','Выйти (admin)'];
    public static $TopMenuProperty = 'ul[class="navbar-nav navbar-right nav"]';

    public static $PageTexts = 'Заявки';
    public static $PageTextsProperty = 'h1';

    public static $NextPageLink = '»';
    public static $PreviousPageLink = '«';
    public static $PaginatorProperty = 'div[class="paginator"]';

    public static $FilterIDField = 'input[name="searchId"]';
    public static $FilterTextField = 'input[name="searchText"]';
    public static $FilterStatusField = 'select[name="searchStatus"]';
    public static $NoRecordsFind = 'Ничего не найдено.';

    public static $EditCommentButton = 'Редактировать';
    public static $EditConfirmButton = 'Сохранить';
    public static $EditCancelButton = 'Отмена';

    public static $HandleCommentButton = 'Обработать';
    public static $HandleWindowTitle = 'Вы действительно хотите подтвердить заявку №';
    public static $HandleConfirmButton = 'Подтвердить';
    public static $HandleCancelButton = 'Отмена';

    public static $NoRecords = 'Ничего не найдено.';


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

    public function SeeRequests(array $Data, $ID)
    {
        foreach ($Data as $value) {
            $this->acceptanceTester->see($value,'div[data-id="' . $ID . '"]');
        }
    }

    public function dontSeeRequests(array $Data, $ID)
    {
        foreach ($Data as $value) {
            $this->actor->dontSee($value,'div[data-id="' . $ID . '"]');
        }
    }

    public function SeeComment($Text, $ID)
    {
        $this->acceptanceTester->seeInField('textarea[data-id="'. $ID .'"]',$Text);
    }

    // $Text = false - если не хотим менять комментарий
    public function EditComment($Text,$ID,$SaveOrCacel)
    {
        $this->acceptanceTester->click(self::$EditCommentButton, 'div[data-id="' . $ID . '"]');
        $this->acceptanceTester->see(self::$EditConfirmButton, 'div[data-id="' . $ID . '"]');
        $this->acceptanceTester->see(self::$EditCancelButton, 'div[data-id="' . $ID . '"]');
        if ( $Text !== false ) $this->acceptanceTester->fillField('textarea[data-id="'. $ID .'"]',$Text);
        $Button = ($SaveOrCacel === true) ? self::$EditConfirmButton : self::$EditCancelButton;
        $this->acceptanceTester->click($Button, 'div[data-id="' . $ID . '"]');
    }

    public function Handle($ID,$SaveOrCacel)
    {
        $this->acceptanceTester->click(self::$HandleCommentButton, 'div[data-id="' . $ID . '"]');
        if (method_exists($this->acceptanceTester, 'wait')) { $this->acceptanceTester->wait(1); } // only for selenium
        $this->acceptanceTester->see(self::$HandleWindowTitle.$ID,'div[class="modal-content"]');
        $Button = ($SaveOrCacel === true) ? self::$HandleConfirmButton : self::$HandleCancelButton;
        $this->acceptanceTester->click($Button, 'div[class="modal-content"]');
    }

    public function ApplyIDFilter($ID)
    {
        $this->acceptanceTester->fillField(self::$FilterIDField,$ID);
        $this->acceptanceTester->pressKey(self::$FilterIDField,\Facebook\WebDriver\WebDriverKeys::ENTER);
    }

    public function ApplyTextFilter($Text)
    {
            $this->acceptanceTester->fillField(self::$FilterTextField,$Text);
            $this->acceptanceTester->pressKey(self::$FilterTextField,\Facebook\WebDriver\WebDriverKeys::ENTER);
    }

    public function ApplyStatusFilter($Status)
    {
            $this->acceptanceTester->selectOption(self::$FilterStatusField,$Status);
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

    public function GrabCreateTime()
    {
        return str_replace('Оформлена: ','',$this->acceptanceTester->grabMultiple('div[class="itemBlock"] div[class="itemDateCreated"]'));
    }

}
