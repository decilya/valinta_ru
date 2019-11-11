<?php

use yii\db\Migration;

class m190412_120329_createShowUserContactsCounter extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('show_user_contacts_counter', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('show_user_contacts_counter');
    }
}
