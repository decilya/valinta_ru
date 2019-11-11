<?php

namespace app\models\traits;

use Yii;
use yii\db\ActiveRecordInterface;

/**
 * Трейт предназначен для таблиц ActiveRecord, у которых есть поле created_at
 */
trait SetCreatedAtTrait
{
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $this->created_at = time();


            return true;
        }
        return false;
    }

    public function beforeValidate()
    {
        $this->created_at = time();

        return parent::beforeValidate();
    }
}