<?php

use yii\db\Migration;

/**
 * Class m191105_115911_fixEmail
 */
class m191105_115911_fixEmail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('customer', 'email');
        $this->dropColumn('users', 'email');
        $this->dropColumn('rcsc', 'email');

        $this->dropColumn('customer', 'real_id');
        $this->dropColumn('users', 'real_id');
        $this->dropColumn('rcsc', 'real_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('customer', 'email', $this->string(125)->null());
        $this->addColumn('users', 'email', $this->string(129)->null());
        $this->addColumn('rcsc', 'email', $this->string(255)->null());

        $this->addColumn('customer', 'real_id', $this->integer(11)->null());
        $this->addColumn('users', 'real_id', $this->integer(11)->null());
        $this->addColumn('rcsc', 'real_id', $this->integer(11)->null());
    }
}
