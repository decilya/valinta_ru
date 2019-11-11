<?php

namespace app\models;

use app\models\traits\SetCreatedAtTrait;
use Yii;

/**
 * This is the model class for table "requests_has_database".
 *
 * @property integer $id
 * @property integer $request_id
 * @property integer $database_id
 * @property integer $created_at
 */
class RequestsHasDatabase extends \yii\db\ActiveRecord
{
    use SetCreatedAtTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'requests_has_database';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['request_id', 'database_id'], 'required'],
            [['request_id', 'database_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_id' => 'Request ID',
            'database_id' => 'Database ID',
            'created_at' => 'Created At',
        ];
    }
}