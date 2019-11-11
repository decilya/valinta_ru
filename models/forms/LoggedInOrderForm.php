<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

/**
 * @property integer $login
 */
class LoggedInOrderForm extends Model
{
    public $login;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login'], 'required' , 'message' => 'Это обязательное поле'],

        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => 'Адрес электронной почты (Вы уже зарегистрированы)',
        ];
    }


}