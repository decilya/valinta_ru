<?php

namespace app\models;

use app\models\traits\SetCreatedAtTrait;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "customer_phone".
 *
 * @property integer $id
 * @property string $phone
 * @property integer $created_at
 * @property integer $customer_id
 *
 * @property Customer $customer
 */
class CustomerPhone extends ActiveRecord
{
    use SetCreatedAtTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_phone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone'], 'required' , 'message' => 'Поле номера телефона не может быть пустым'],
            [['created_at', 'customer_id'], 'integer'],
            [['phone'], 'string', 'max' => 25],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::className(), 'targetAttribute' => ['customer_id' => 'id']],

            [['phone'], 'match', 'pattern' => '/^79|^\+7\(9/', 'message' => 'Пожалуйста, введите верный номер мобильного телефона.'],
            [['phone'], 'match', 'pattern' => '/^((?!_).)*$/', 'message' => 'Пожалуйста, введите верный номер мобильного телефона.'],
            [['phone'], 'string', 'min' => 11, 'tooShort' => 'Некорректный номер'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'created_at' => 'Время создания',
            'customer_id' => 'Customer ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::className(), ['id' => 'customer_id']);
    }

}