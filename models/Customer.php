<?php

namespace app\models;

use app\components\validators\CustomEmailValidator;
use app\models\traits\SetCreatedAtTrait;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "customer".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property integer $created_at
 * @property integer $status_id
 * @property integer $updated_at
 * @property integer $real_id
 *
 * @property CustomerPhone[] $customerPhones
 * @property CustomerPhone[] $realCustomerPhones
 *
 * @property Auth $real
 *
 * @property string $password
 * @property string $rePassword
 *
 * @property string $status
 * @property string $reason
 *
 * @property string $real_pass
 */
class Customer extends ActiveRecord
{
    // Трейт для работы с полем created_at
    use SetCreatedAtTrait;

    const SCENARIO_REGISTER = 'register';
    const SCENARIO_UPDATE = 'update';

    const STATUS_REQUIRES_VERIFICATION = [
        'val' => 1,
        'title' => 'Требует проверки'
    ];
    const STATUS_CONFIRMED = [
        'val' => 2,
        'title' => 'Подтверждён'
    ];
    const STATUS_REJECTED = [
        'val' => 3,
        'title' => 'Отклонён',
    ];

    // доп. поле для формы, сам $password будет храниться в auth
    public $password;
    public $rePassword;
    public $customerPhones;

    public $phones;

    // если не пустое, то значение будет присвоено для real_id
    public $realIdForSave;
    private $email;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['customerPhones', 'validateCustomerPhones'],

            [['name', 'customerPhones', 'email'], 'required', 'message' => 'Это обязательное поле'],
            [['created_at', 'status_id', 'updated_at', 'real_id'], 'integer'],

            ['name', 'string', 'max' => 125],
            ['email', 'string', 'max' => 125],
            ['reason', 'string', 'max' => 2000],
            ['real_pass', 'string', 'max' => 250],
            ['email', 'validateRealUniqueEmail', 'on' => self::SCENARIO_REGISTER],
            ['email', 'validateRealUniqueEmailUpdate', 'on' => self::SCENARIO_UPDATE],

            ['email', CustomEmailValidator::className(), 'message' => 'Некорректный адрес', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],

            [['password', 'rePassword'], 'required', 'message' => 'Это обязательное поле', 'on' => self::SCENARIO_REGISTER],

            ['password', 'match', 'pattern' => '/[A-ZА-Я]+/', 'message' => 'Пароль должен содержать хотя бы одну заглавную букву', 'on' => self::SCENARIO_REGISTER],
            ['password', 'match', 'pattern' => '/\d+/', 'message' => 'Пароль должен содержать хотя бы одну цифру', 'on' => self::SCENARIO_REGISTER],
            ['password', 'string', 'min' => 6, 'tooShort' => 'Пароль должен быть не менее 6 символов', 'max' => 255, 'tooLong' => 'Пароль должен быть не более 255 символов', 'on' => self::SCENARIO_REGISTER],

            ['rePassword', 'compare', 'compareAttribute' => 'password', 'operator' => '==', 'message' => 'Повторите пароль для подтверждения, пароли не совпадают', 'on' => self::SCENARIO_REGISTER],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'real_id' => '№ профиля Заказчика',
            'name' => 'Ф.И.О.',
            'email' => 'Адрес электронной почты',
            'created_at' => 'Время создания',
            'customerPhones' => 'Мобильный телефон',
            'password' => 'Пароль (должен содержать не менее 6 символов, включая одну заглавную букву и одну цифру)',
            'rePassword' => 'Подтверждение пароля',
            'status_id' => 'Статус',
            'updated_at' => 'Дата изменения',
            'reason' => 'Причина отклонения'
        ];
    }


    public function afterFind()
    {
        parent::afterFind();

        if ($this->id != null) {
            /** @var Auth $auth */
            $auth = Auth::findOne(['customer_id' => $this->id]);

            if (isset($auth->login)) {
                $this->email = $auth->login;
            }
        }
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_REGISTER] = ['password', 'rePassword', 'name', 'email', 'customerPhones'];
        $scenarios[self::SCENARIO_UPDATE] = ['name', 'email', 'customerPhones', 'reason'];

        return $scenarios;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReal()
    {
        return $this->hasOne(Auth::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerPhones()
    {
        return $this->hasMany(CustomerPhone::className(), ['customer_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRealCustomerPhones()
    {
        return $this->hasMany(CustomerPhone::className(), ['customer_id' => 'id']);
    }

    public function getAuth()
    {
        return $this->hasOne(Auth::className(), ['customer_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return CustomerQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CustomerQuery(get_called_class());
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateRealUniqueEmail($attribute, $params)
    {
        if (!empty(Auth::find()->where(['login' => $this->email])->one())) {
            $this->addError($attribute, "E-mail уже зарегистрирован");
        }

        $patter = "|[а-яё]|is";
        if (preg_match($patter, $this->email)) {
            $this->addError($attribute, "Кириллические символы недопустимы в email");
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateRealUniqueEmailUpdate($attribute, $params)
    {
        $auth = Auth::find()->where(['customer_id' => $this->email])->one();
        if (!empty($auth)) {
            if ($auth->login != $this->email) {
                if (!empty(Auth::find()->where(['login' => $this->email])->one())) {
                    $this->addError($attribute, "E-mail уже зарегистрирован");
                }
            }
        }

        $patter = "|[а-яё]|is";
        if (preg_match($patter, $this->email)) {
            $this->addError($attribute, "Кириллические символы недопустимы в email");
        }
    }

    public function validateCustomerPhones($attribute, $params)
    {
        foreach ($this->customerPhones as $myCustomerPhone) {

            $tmp = new CustomerPhone([
                'phone' => $myCustomerPhone,
                'customer_id' => $this->id,
            ]);

            if (!$tmp->validate()) {

                foreach ($tmp->errors as $messagesErr) {

                    foreach ($messagesErr as $msgErr) {
                        $this->addError($attribute, $msgErr);
                    }
                }
            }
        }
    }


    public function setCustomerPhones()
    {
        if (!empty($this->customerPhones)) {
            foreach ($this->customerPhones as $customerPhoneItem) {
                $customerPhoneModel = new CustomerPhone([
                    'phone' => (string)$customerPhoneItem,
                    'customer_id' => $this->id,
                ]);

                if ($customerPhoneModel->validate()) {
                    $customerPhoneModel->save(false);
                } else {

                    // уже сохранившиеся $customerPhone будут удалены самой MySql так как связаны по ключу
                    $this->delete();
                    Yii::$app->session->setFlash('error', 'К сожалению, не получилось сохранить запись');

                    break;
                }
            }
        }

        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->refresh();

        if ($this->scenario == Auth::SCENARIO_REGISTER) {

            if ($this->setCustomerPhones()) {

                if (is_null($a = Auth::find()->where(['customer_id' => $this->id])->one())) {

                    $auth = new Auth(['scenario' => self::SCENARIO_REGISTER]);
                    $auth->login = $this->email;
                    $pas = Yii::$app->getSecurity()->generatePasswordHash($this->password);
                    $auth->password = $pas;
                    $auth->user_id = null;
                    $auth->customer_id = $this->id;

                    if ($auth->save()) {

                        $customer = Customer::find()->where(['id' => $this->id])->one();

                        $customer->real_id = ($this->realIdForSave != null) ? $this->realIdForSave : $auth->id;

                        if (!$customer->update(false)) {
                            Yii::$app->session->setFlash('error', 'К сожалению, не получилось сохранить значение realID');
                            $this->delete();
                            return false;
                        }
                    } else {
                        $this->delete();
                        Yii::$app->session->setFlash('error', 'К сожалению, не получилось сохранить запись');
                    }
                }
            }

        } elseif ($this->scenario == self::SCENARIO_UPDATE) {
            if ($this->setCustomerPhones()) {

                /** @var Auth $auth */
                $auth = Auth::findOne(['id' => $this->real_id]);
                $auth->login = $this->email;

                if ($auth->login != $auth->getDirtyAttributes(['login'])) {
                    $auth->scenario = Auth::SCENARIO_REGISTER;
                    $auth->save();

                    $this->status_id = self::STATUS_REQUIRES_VERIFICATION['val'];
                }
            }
        }

        return true;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if (is_null($this->status_id)) {
                $this->status_id = $this::STATUS_REQUIRES_VERIFICATION['val'];
            }

            if (is_null($this->real_id)) {
                $this->real_id = 0; // на время присвоим пока 0, а после сейва auth присвоим верное значение...
            }

            if (is_null($this->created_at)) {
                $this->created_at = time();
            }

            if (is_null($this->updated_at) && (!is_null($this->created_at))) {
                $this->updated_at = $this->created_at;
            } else {
                $this->updated_at = time();
            }

            if (is_null($this->reason)) {
                $this->reason = '';
            } else {
                $this->reason = strip_tags(trim($this->reason));
            }

            $this->email = strip_tags(trim($this->email));
            $this->name = strip_tags(trim($this->name));

            $oldAttributes = $this->getOldAttributes();

            if (!empty($oldAttributes)) {
                if (($this->name != $oldAttributes['name'])) {
                    $this->status_id = self::STATUS_REQUIRES_VERIFICATION['val'];
                }
            }

            return true;
        }

        return false;
    }


    /**
     * Constructs proper query for admin pages filtering.
     *
     * @param array $qp Received request query string params.
     * @param ActiveRecord $model
     * @return array $arr Array with filter parameters and actual query.
     */
    public static function constructAdminFiltersQuery($qp, $model = null)
    {
        if (is_null($model)) $model = new Customer();

        $arr = [
            'filterParams' => []
        ];

        $query = $model::find();

        if (!empty($qp['id'])) {
            $query->andFilterWhere(['id' => (int)$qp['id']]);

            $arr['filterParams']['id'] = (int)$qp['id'];
        }

        if (!empty($qp['text'])) {

            $text = (!empty($qp['text']) ? strip_tags(trim($qp['text'])) : "");

            if ($model instanceof Customer) {
                $query->andFilterWhere(['or', ['like', 'name', $text], ['like', 'email', $text]]);

                $phone = Phone::find()->where([
                    'like',
                    'number',
                    $text
                ])->indexBy('id')->all();

                if (!empty($phone)) {
                    $query->orFilterWhere(['in', 'id', (new Query())->select('customer_id')->from('customer_phone')->where(['in', 'phone_id', array_keys($phone)])]);
                }
            } else {
                $query->andFilterWhere(['or', ['like', 'name', $text], ['like', 'email', $text], ['like', 'phone', $text]]);
            }

            $arr['filterParams']['text'] = $text;
        }

        if (!empty($qp['userStatus'])) {
            $query->andFilterWhere(['status' => (int)$qp['userStatus']]);
            $arr['filterParams']['userStatus'] = (int)$qp['userStatus'];
        }

        if (!empty($qp['status'])) {
            $query->andFilterWhere(['status_value' => (int)$qp['status']]);
            if ($model instanceof Request) $arr['filterParams']['status'] = (!empty($qp['status'])) ? (int)$qp['status'] : "";
        }

        if (!empty($qp['page'])) $arr['filterParams']['page'] = $qp['page'];

        $arr['query'] = $query;

        return $arr;
    }

    public function getPhones()
    {
        if ($this->id == null) return [];

        /** @var Customer $customer */
        $customer = Customer::find()->where(['id' => $this->id])->with('customerPhones')->one();
        return isset($customer->relatedRecords['customerPhones']) ? $customer->relatedRecords['customerPhones'] : null;
    }

    /**
     * Значние статуса Заказчика
     *
     * @return string $str;
     */
    public function getStatus()
    {
        switch ($this->status_id) {
            case 1:
                $str = $this::STATUS_REQUIRES_VERIFICATION['title'];
                break;
            case 2:
                $str = $this::STATUS_CONFIRMED['title'];
                break;
            case 3:
                $str = $this::STATUS_REJECTED['title'];
                break;
            default:
                $str = '';;
        }

        return $str;
    }

    public function __get($property)
    {
        if ($property === 'email') {
            return $this->email;
        } else {
            return parent::__get($property);
        }
    }

    public function __set($property, $value)
    {
        if ($property === 'email') {
            $this->email = $value;
        } else {
            parent::__set($property, $value);
        }
    }

    public function getReal_id()
    {
        /** @var Auth $auth */
        $auth = Auth::findOne(['customer_id' => $this->id]);

        if (!empty($auth)) {
            return $auth->id;
        }

        return null;
    }

    public function setReal_id($id)
    {
        if ($id !== null) {
            $this->real_id = $id;
        }
    }
}