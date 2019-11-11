<?php

use yii\db\Migration;

class m190722_085208_fixCustomer extends Migration
{
    public function safeUp()
    {
        $this->addColumn('customer', 'real_pass', $this->string(250)->null());

    }

    public function safeDown()
    {
        $this->dropColumn('customer', 'real_pass');
    }

}
