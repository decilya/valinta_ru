<?php

use yii\db\Migration;

class m190625_095007_fixRequests extends Migration
{
    public function safeUp()
    {
        $this->addColumn('requests', 'inn', $this->string(12)->null());
        $this->addColumn('requests', 'access_days', $this->integer(2)->notNull()->defaultValue(1));
        $this->addColumn('requests', 'cost', $this->integer(11)->notNull()->defaultValue(0));
        $this->addColumn('requests', 'text', $this->string(500)->null()->defaultValue(''));
        $this->addColumn('requests', 'desired_date', $this->integer(11)->notNull());
    }

    public function safeDown()
    {
        $this->dropColumn('requests', 'desired_date');
        $this->dropColumn('requests', 'inn');
        $this->dropColumn('requests', 'access_days');
        $this->dropColumn('requests', 'cost');
        $this->dropColumn('requests', 'text');
    }
}
