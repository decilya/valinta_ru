<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_has_smeta_docs".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $smeta_docs_id
 */
class UserHasSmetaDocs extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_has_smeta_docs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'smeta_docs_id'], 'integer'],
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
            'smeta_docs_id' => 'Smeta Docs ID',
        ];
    }
}
