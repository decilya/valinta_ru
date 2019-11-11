<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_has_smeta_docs".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $smeta_docs_id
 */
class OrderHasSmetaDocs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_has_smeta_docs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'smeta_docs_id'], 'required'],
            [['order_id', 'smeta_docs_id'], 'integer'],
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
            'smeta_docs_id' => 'Smeta Docs ID',
        ];
    }
}