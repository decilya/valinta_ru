<?php

namespace app\models;

use app\models\traits\SetCreatedAtTrait;
use Yii;

/**
 * This is the model class for table "rcsc_has_database".
 *
 * @property int $id
 * @property int $rcsc_id
 * @property int $database_id
 * @property int $created_at
 */
class RcscHasDatabase extends \yii\db\ActiveRecord
{
    use SetCreatedAtTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rcsc_has_database';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rcsc_id', 'database_id', 'created_at'], 'required'],
            [['rcsc_id', 'database_id', 'created_at', 'id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rcsc_id' => 'Rcsc ID',
            'database_id' => 'Database ID',
            'created_at' => 'Created At',
        ];
    }
}