<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_has_professions".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $profession_id
 */
class UserHasProfessions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_has_professions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'profession_id'], 'integer'],
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
            'profession_id' => 'Profession ID',
        ];
    }
}
