<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "database".
 *
 * @property integer $id
 * @property string $name
 * @property integer $cost
 * @property string $real;
 */
class Database extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'database';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['cost'], 'integer'],
            [['name', 'realName'], 'string', 'max' => 250],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'cost' => 'Cost',
        ];
    }

    public function getRealName()
    {
        return $this->name . ' - ' . $this->cost . 'р.';
    }
}