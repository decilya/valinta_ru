<?php

use yii\db\Migration;

class m180920_084113_createCustomerAndFixAuth extends Migration
{
    public function safeUp()
    {
        $this->createTable('customer', [
            'id' => $this->primaryKey(),
            'name' => $this->string(125)->notNull(),
            'email' => $this->string(125)->notNull(),
            'created_at' => $this->integer(12)->null(),
        ], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

        $this->addColumn('auth', 'customer_id', $this->integer(11)->null());
    }

    public function safeDown()
    {
        $this->dropTable('customer');

        $this->dropColumn('auth', 'customer_id');
    }
}
