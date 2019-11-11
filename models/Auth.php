<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth".
 *
 * @property integer $id
 * @property string $login
 * @property string $password
 * @property int $last_auth
 * @property int $is_admin
 * @property int $is_user
 * @property int $user_id
 * @property int $recovery_token
 * @property int $customer_id
 * @property int $rcsc_id
 */
class Auth extends ActiveRecord implements IdentityInterface
{
    const SCENARIO_REGISTER = 'register';
    const SCENARIO_RECOVER_PASS = 'recover';
    const SCENARIO_CHANGE_PASS = 'change';
    const SCENARIO_AUTHORIZATION = 'authorization';

    //  0 - гость, 1 - сметчик, 2 - заказчик, 3 - админ, 4 - РЦЦС
    const TYPE_GUEST = 0;
    const TYPE_USER = 1;
    const TYPE_CUSTOMER = 2;
    const TYPE_ADMIN = 3;
    const TYPE_RCSC = 4;

    const STRING_GUEST = 'guest';
    const STRING_USER = 'user';
    const STRING_CUSTOMER = 'customer';
    const STRING_ADMIN = 'admin';
    const STRING_RCSC = 'rcsc';

    public function scenarios()
    {
        return [
            self::SCENARIO_REGISTER => ['login', 'password', 'last_auth', 'is_admin', 'is_user', 'user_id', 'recovery_token', 'customer_id'],
            self::SCENARIO_RECOVER_PASS => ['login', 'recovery_token'],
            self::SCENARIO_CHANGE_PASS => ['pass_change', 'pass_change_repeat', 'token'],
            self::SCENARIO_AUTHORIZATION => ['login', 'password'],
        ];
    }

    private static $users;

    public $error;

    public $pass_change;
    public $pass_change_repeat;
    public $token;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password'], 'required', 'on' => self::SCENARIO_REGISTER],
            [['last_auth', 'is_admin', 'is_user', 'user_id', 'customer_id'], 'integer', 'on' => self::SCENARIO_REGISTER],
            [['login', 'password'], 'string', 'max' => 255, 'on' => self::SCENARIO_REGISTER],
            [['recovery_token'], 'string', 'max' => 50, 'on' => self::SCENARIO_REGISTER],


            ['login', 'required', 'message' => '* это обязательное поле', 'on' => self::SCENARIO_RECOVER_PASS],
            ['login', 'exist', 'filter' => 'is_user = 1', 'message' => 'Пользователь не найден', 'on' => self::SCENARIO_RECOVER_PASS],

            [['pass_change', 'pass_change_repeat', 'token'], 'required', 'message' => '* это обязательное поле', 'on' => self::SCENARIO_CHANGE_PASS],
            ['pass_change', 'match', 'pattern' => '/[A-ZА-Я]+/', 'message' => 'Пароль должен содержать хотя бы одну заглавную букву', 'on' => self::SCENARIO_CHANGE_PASS],
            ['pass_change', 'match', 'pattern' => '/\d+/', 'message' => 'Пароль должен содержать хотя бы одну цифру', 'on' => self::SCENARIO_CHANGE_PASS],
            ['pass_change', 'string', 'min' => 6, 'tooShort' => 'Пароль должен быть не менее 6 символов', 'max' => 255, 'tooLong' => 'Пароль должен быть не более 255 символов', 'on' => self::SCENARIO_CHANGE_PASS],
            ['pass_change_repeat', 'compare', 'compareAttribute' => 'pass_change', 'operator' => '==', 'message' => 'Пароли не совпадают', 'on' => self::SCENARIO_CHANGE_PASS],


            [['login', 'password'], 'required', 'message' => '* это обязательное поле', 'on' => self::SCENARIO_AUTHORIZATION],
            ['password', 'validatePassword', 'on' => self::SCENARIO_AUTHORIZATION],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => ($this->scenario == 'recover') ? 'Укажите ваш e-mail' : 'Логин',
            'password' => 'Пароль',
            'last_auth' => 'Last Auth',
            'is_admin' => 'Is Admin',
            'is_user' => 'Is User',
            'pass_change' => 'Пароль (должен содержать не менее 6 символов, включая одну заглавную букву и одну цифру)',
            'pass_change_repeat' => 'Подтверждение пароля',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        self::$users = self::getUsers();
        return isset(self::$users[$id]) ? new static(self::$users[$id]) : null;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {

    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        self::$users = self::getUsers();
        foreach (self::$users as $user) {
            if (strcasecmp($user['login'], $username) === 0) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {

    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {

    }


    public static function getUsers()
    {
        $return = [];
        $model = self::find()->all();
        foreach ($model as $user) {
            $result = [
                'id' => $user->id,
                'login' => $user->login,
                'password' => $user->password,
                'last_auth' => $user->last_auth,
                'is_admin' => $user->is_admin,
                'is_user' => $user->is_user,
                'user_id' => $user->user_id
            ];
            $return[$user->id] = $result;
        }
        return $return;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePass($this->password)) {
                $this->addError('error', 'Неправильный логин или пароль');
                $this->addError('login', '');
                $this->addError('password', '');
            }
        }
    }

    public function validatePass($password)
    {

        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = self::findByUsername($this->login);
        }
        return $this->_user;
    }

    public function getCustomer()
    {
        if ($this->customer_id == null) return null;

        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

    /**
     * Creates Auth model tied with User model.
     *
     * @param $user
     * @return Auth
     * @throws \yii\base\Exception
     */
    public static function createAuthData($user)
    {
        $auth = new self(['scenario' => self::SCENARIO_REGISTER]);

        $auth->login = $user->email;
        $auth->password = Yii::$app->getSecurity()->generatePasswordHash($user->password);
        $auth->user_id = $user->id;

        return $auth;
    }

    /**
     * Вернуть тип пользователя в системе
     *
     * @return int
     */
    public static function getUserType(): int
    {
        /** @var int $userType 0 - гость, 1 - сметчик, 2 - заказчик 3 - админ */

        if (!Yii::$app->user->isGuest) {

            return self::getUserTypeById(Yii::$app->user->identity->id);

        }

        return Auth::TYPE_GUEST;
    }

    /**
     * Вернуть тип пользователя в системе по realId
     *
     * @param $id
     * @return int
     */
    public static function getUserTypeById($id)
    {
        /** @var Auth $auth */
        $auth = Auth::find()->where(['id' => $id])->one();

        if (!empty($auth)) {

            if ($auth->is_admin) {
                return Auth::TYPE_ADMIN;
            } elseif ($auth->is_user == 1) {

                if (!empty(Customer::find()->where(['id' => $auth->customer_id])->one())) {
                    return Auth::TYPE_CUSTOMER;
                }

                if (!empty(User::find()->where(['id' => $auth->user_id])->one())) {
                    return Auth::TYPE_USER;
                }

                if (!empty(Rcsc::find()->where(['id' => $auth->rcsc_id])->one())) {
                    return Auth::TYPE_RCSC;
                }
            }
        }

        return Auth::TYPE_GUEST;
    }


    /** Получить id Auth или 0 гость */
    public static function getUserRealId()
    {
        if (!Yii::$app->user->isGuest) {
            return Yii::$app->user->identity->id;
        }

        return null;
    }

    public function isUser()
    {
        return (bool)$this->user_id;
    }


}