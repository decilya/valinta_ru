<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "user_review_order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $order_id
 * @property integer $status_id
 */
class UserReviewOrder extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_OK = 1;
    const STATUS_NO = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_review_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'order_id'], 'required'],
            [['user_id', 'order_id', 'status_id'], 'integer'],
        ];
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
            'status_id' => 'Status ID',
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

            if ($this->status_id == null){
                $this->status_id = self::STATUS_NEW;
            }

            return true;
        }

        return false;
    }

}