<?php

use yii\db\Migration;

class m190701_092041_fixRequest extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('requests', 'text');
    }

    public function safeDown()
    {
        $this->addColumn('requests', 'text', $this->string(500)->null()->defaultValue(''));
    }

}
