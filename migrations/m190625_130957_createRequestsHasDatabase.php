<?php

use yii\db\Migration;

class m190625_130957_createRequestsHasDatabase extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('requests_has_database', [
            'id' => $this->primaryKey(),
            'request_id' => $this->integer(11)->notNull(),
            'database_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->null()
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('requests_has_database');
    }

}
