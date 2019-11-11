<?php

use yii\db\Migration;

class m190305_112634_createUserFeedBackOrder extends Migration
{
    public function safeUp()
    {
        $this->createTable('order_feadback_user', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'order_id' => $this->integer(11)->notNull(),
            'customer_id' => $this->integer(11)->notNull(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('order_feadback_user');
    }
}