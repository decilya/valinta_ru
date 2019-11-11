<?php

use yii\db\Migration;

class m190304_092441_delLinkFromUser extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('order', 'link');
    }

    public function safeDown()
    {
       $this->addColumn('order', 'link', $this->string(255)->notNull());
    }
}
