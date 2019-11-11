<?php

namespace app\models\forms;

use app\components\validators\CustomEmailValidator;
use app\models\Auth;
use app\models\User;
use Yii;
use yii\base\Model;

/**
 * @property integer $login
 * @property integer $password
 */
class FastRegOrderForm extends Model
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
            [['login', 'password', 'rePassword'], 'required' , 'message' => 'Это обязательное поле'],
            ['login', CustomEmailValidator::className(), 'message' => 'Некорректный адрес'],
            ['login', 'unique', 'message' => 'E-mail уже зарегистрирован'],
            ['login', 'validateisUserEmail', 'message' => 'E-mail уже зарегистрирован'],

            ['password', 'match', 'pattern' => '/[A-ZА-Я]+/', 'message' => 'Пароль должен содержать хотя бы одну заглавную букву'],
            ['password', 'match', 'pattern' => '/\d+/', 'message' => 'Пароль должен содержать хотя бы одну цифру'],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Пароль должен быть не менее 6 символов', 'max' => 255, 'tooLong' => 'Пароль должен быть не более 255 символов'],

            ['rePassword', 'compare', 'compareAttribute' => 'password','operator' => '==','message' => 'Повторите пароль для подтверждения'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'login' => 'Адрес электронной почты',
            'password' => 'Пароль (должен содержать не менее 6 символов, включая одну заглавную букву и одну цифру)',
            'rePassword' => 'Подтверждение пароля',
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateisUserEmail($attribute, $params)
    {
        if ($this->getDirtyAttributes(['login'])) {

            if (!empty(User::find()->where(['email' => $this->login])->one())) {
                $this->addError($attribute, "Такой сметичк уже зарегистирован в системе");
            }

            $patter = "|[а-яё]|is";
            if (preg_match($patter, $this->login)) {
                $this->addError($attribute, "Кириллические символы недопустимы в email");
            }
        }
    }


}