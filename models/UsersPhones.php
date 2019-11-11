<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_phones".
 *
 * @property int $id
 * @property int $user_id
 * @property int $phone_id
 * @property int $index
 * @property int $is_new
 */
class UsersPhones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_phones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'phone_id', 'index'], 'required'],
            [['user_id', 'phone_id', 'index', 'is_new'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'phone_id' => 'Phone ID',
            'index' => 'Index',
            'is_new' => 'Is New',
        ];
    }
}