<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_has_professions".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $professions_id
 */
class OrderHasProfessions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_has_professions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'professions_id'], 'required'],
            [['order_id', 'professions_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'professions_id' => 'Professions ID',
        ];
    }
}