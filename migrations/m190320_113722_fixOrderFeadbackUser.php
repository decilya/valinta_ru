<?php

use yii\db\Migration;

class m190320_113722_fixOrderFeadbackUser extends Migration
{
    public function safeUp()
    {
        $this->addColumn('order_feadback_user', 'new', $this->integer(1)->defaultValue(1)->null());
    }

    public function safeDown()
    {
       $this->dropColumn('order_feadback_user', 'new');
    }
}
