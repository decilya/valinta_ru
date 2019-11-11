<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Это не отклики Сметчиков, это Подходящие Сметчикам Заказы
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property int $user_updated
 */
class OrderUser extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'user_id'], 'required'],
            [['order_id', 'user_id', 'user_updated'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'user_id' => 'User ID',
            'user_updated' => 'User Updated',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function beforeValidate()
    {
        if ($this->user_id) {
            $user = User::find()->where(['id' => $this->user_id])->one();
            if (!empty($user)) {
                if ($user instanceof User) {

                    $this->user_updated = $user->date_changed;
                }
            }
        }

        return parent::beforeValidate();
    }

}