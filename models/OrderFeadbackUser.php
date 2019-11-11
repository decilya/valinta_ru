<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_feadback_user".
 *
 * @property int $id
 * @property int $user_id
 * @property int $order_id
 * @property int $customer_id
 * @property int $send
 * @property int $created_at
 * @property int $new
 *
 * @property int $percent
 *
 * @property User $user
 * @property Order $order
 *
 */
class OrderFeadbackUser extends \yii\db\ActiveRecord
{

    //  public $percent;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_feadback_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'order_id', 'customer_id'], 'required'],
            [['user_id', 'order_id', 'customer_id', 'send', 'created_at', 'new'], 'integer'],
        ];
    }

    /**
     * @return int
     */
    public function getPercent()
    {
        return Site::calcPercentUserByOrder($this->user, $this->order);
    }

    /**
     * @param $value
     */
    public function setPercent($value)
    {
        $this->percent = $value;
    }

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'order_id' => 'Order ID',
            'customer_id' => 'Customer ID',
            'send' => 'Send',
            'created_at' => 'Created At',
            'new' => 'Новый отклик'
        ];
    }

    /**
     *
     * Перед сохранением заказа
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if (is_null($this->created_at)) {
                $this->created_at = time();
            }

            // а вот тут пожалуй строго ===, а не is_null, вдруг чего приведет не того хз, не уверен
            if (($this->new === null) && ($this->isNewRecord)) {
                $this->new = 1;
            }

            return true;
        }

        return false;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    /**
     * Отклик просмотрен
     *
     * @return bool
     */
    public function nowIsVisited()
    {
        if ($this->new == 1) {
            $this->new = 0;

            if ($this->save()) return true;
        }

        return false;
    }


}