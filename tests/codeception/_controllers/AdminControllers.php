<?php

namespace tests\codeception\_controllers;

//use yii\codeception\BasePage;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class AdminControllers
{

    protected $actor;

    private static $TopMenuExitLink = 'Выйти (admin)';

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

/*    public function __construct($I)
    {
        $this->actor = $I;
    }*/

    public function GoToPage($Page,$Property)
    {
        $this->actor->click($Page,$Property);
    }

/*    public function CleanFilter(array $FilterData)
    {
        foreach ($FilterData as $field) {
            if (strpos($field,'input') === 0 ) {
                $this->actor->pressKey($field,array('ctrl', 'a'), \Facebook\WebDriver\WebDriverKeys::DELETE);
                $this->actor->pressKey($field,\Facebook\WebDriver\WebDriverKeys::ENTER);
            }
            if (strpos($field,'select') === 0 ) {
                $this->actor->selectOption($field,'все');
            }
            if (method_exists($this->actor, 'wait')) { $this->actor->wait(1); } // only for selenium
        }
    }*/

    public function Logout()
    {
        $this->acceptanceTester->click(AdminControllers::$TopMenuExitLink);
    }
}
