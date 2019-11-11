<?php

namespace tests\codeception\_controllers;

//use tests\codeception\_pages\Smet4ikPage;

/**
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class Smet4ikControllers
{

    protected $actor;

//    private static $FieldID_Prof = '#user-professions';
//    private static $FieldID_Smeta = '#user-smetadocs';
//    private static $FieldID_Base = '#user-normbases';
    private static $LinkLogout = 'Выйти';

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
/*
    public function FillRegisterForm(array $HumanData)
    {
        foreach ($HumanData as $field => $value) {
            if ( !empty($value) ) {
                if (strpos($field, Smet4ikPage::$FormRegisterField_City) !== false) {
                    $this->actor->click('span[id="select2-user-city_id-container"]');
                    $this->actor->fillField('.select2-search--dropdown input[class="select2-search__field"]', $value);
                    $this->actor->pressKey('.select2-search--dropdown input[class="select2-search__field"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
                } elseif (strpos($field, Smet4ikPage::$FormRegisterField_Prof) !== false) {
                    foreach ($value as $array_value) {
                        $this->actor->fillField(Smet4ikControllers::$FieldID_Prof . ' + span ul li input[type="search"]', $array_value);
                        $this->actor->pressKey(Smet4ikControllers::$FieldID_Prof . ' + span ul li input[class="select2-search__field"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
                    }
                } elseif (strpos($field, Smet4ikPage::$FormRegisterField_Docs) !== false) {
                    foreach ($value as $array_value) {
                        $this->actor->fillField(Smet4ikControllers::$FieldID_Smeta . ' + span ul li input[type="search"]', $array_value);
                        $this->actor->pressKey(Smet4ikControllers::$FieldID_Smeta . ' + span ul li input[class="select2-search__field"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
                    }
                } elseif (strpos($field, Smet4ikPage::$FormRegisterField_Bases) !== false) {
                    foreach ($value as $array_value) {
                        $this->actor->fillField(Smet4ikControllers::$FieldID_Base . ' + span ul li input[type="search"]', $array_value);
                        $this->actor->pressKey(Smet4ikControllers::$FieldID_Base . ' + span ul li input[class="select2-search__field"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
                    }
                } elseif (strpos($field, Smet4ikPage::$FormRegisterField_Agreement_id) !== false) {
                    $this->actor->checkOption(Smet4ikPage::$FormRegisterField_Agreement_id);
                } else {
                    $this->actor->fillField($field, $value);
                }
            }
        }
        $this->actor->click(Smet4ikPage::$FormRegisterButton,Smet4ikPage::$FormRegisterName);
    }
*/

    public function Logout()
    {
        $this->acceptanceTester->click(Smet4ikControllers::$LinkLogout);
    }

}
