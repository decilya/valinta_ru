<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_has_normative_bases".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $normative_bases_id
 */
class OrderHasNormativeBases extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_has_normative_bases';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'normative_bases_id'], 'required'],
            [['order_id', 'normative_bases_id'], 'integer'],
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
            'normative_bases_id' => 'Normative Bases ID',
        ];
    }
}