<?php

use yii\db\Migration;

class m181120_142517_FixCustomerReason extends Migration
{
    public function safeUp()
    {
        $this->addColumn('customer', 'reason', $this->text()->null());
        $this->renameColumn('order', 'user_id', 'auth_id');

    }

    public function safeDown()

    {
        $this->dropColumn('customer', 'reason');
        $this->renameColumn('order', 'auth_id', 'user_id');
    }
}
