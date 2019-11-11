<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_has_normative_bases".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $normative_bases_id
 */
class UserHasNormativeBases extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_has_normative_bases';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'normative_bases_id'], 'integer'],
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
            'normative_bases_id' => 'Normative Bases ID',
        ];
    }
}
