<?php

use yii\db\Migration;

/**
 * Class m190821_110617_createRCSC
 */
class m190821_110617_createRCSC extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('rcsc', [
            'id' => $this->primaryKey(),
            'email' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'status_id' => $this->integer(1)->defaultValue(1)->notNull(),
            'created_at' => $this->integer(11)->null(),
            'real_id' => $this->integer(11)->null(),
        ]);

        $this->createTable('rcsc_has_database', [
            'id' => $this->primaryKey(),
            'rcsc_id' => $this->integer(11)->notNull(),
            'database_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->notNull(),
        ]);

        $this->addColumn('auth', 'rcsc_id', $this->integer(11)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('rcsc_has_database');
        $this->dropTable('rcsc');

        $this->dropColumn('auth', 'rcsc_id');

    }
}
