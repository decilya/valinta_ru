<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;

/**
 * @property integer $login
 * @property integer $password
 */
class FastLoginOrderForm extends Model
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_UPDATE = 'update';

    public $login;
    public $password;
    public $rePassword;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['login', 'password'], 'required' , 'message' => 'Это обязательное поле'],

            ['password', 'match', 'pattern' => '/[A-ZА-Я]+/', 'message' => 'Пароль должен содержать хотя бы одну заглавную букву', 'on' => self::SCENARIO_REGISTER],
            ['password', 'match', 'pattern' => '/\d+/', 'message' => 'Пароль должен содержать хотя бы одну цифру', 'on' => self::SCENARIO_REGISTER],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Пароль должен быть не менее 6 символов', 'max' => 255, 'tooLong' => 'Пароль должен быть не более 255 символов', 'on' => self::SCENARIO_REGISTER],

            ['rePassword', 'compare', 'compareAttribute' => 'password','operator' => '==','message' => 'Повторите пароль для подтверждения', 'on' => self::SCENARIO_REGISTER],
        ];
    }

    // Адрес электронной почты (Вы уже зарегистрированы)

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => 'Адрес электронной почты (Вы уже зарегистрированы)',
            'password' => 'Пароль от учётной записи',
        ];
    }


}