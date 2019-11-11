<?php

namespace app\models;

use \yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Customer]].
 *
 * @see Customer
 */
class CustomerQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return Customer[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Customer|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}