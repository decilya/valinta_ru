<?php

use yii\db\Migration;

class m181122_133355_UserReviewOrder extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('user_review_order', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer(11)->notNull(),
                'order_id' => $this->integer(11)->notNull(),
                'status_id' => $this->integer(1)->defaultValue(0)->notNull()
            ]
            , $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('user_review_order');
    }
}
