<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "phones".
 *
 * @property integer $id
 * @property string $number
 */
class Phone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'phones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number'], 'required'],
            [['number'], 'string', 'max' => 20],
            [['number'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
        ];
    }

	public function getUsers(){
		return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('users_phones', ['phone_id' => 'id']);
	}

	public function getOrders(){
		return $this->hasMany(Order::className(), ['id' => 'order_id'])->viaTable('order_phones', ['phone_id' => 'id']);
	}


}
