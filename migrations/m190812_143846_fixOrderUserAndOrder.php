<?php

use yii\db\Migration;

/*
 * Class m190812_143846_fixOrderUserAndOrder
 */
class m190812_143846_fixOrderUserAndOrder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('order', 'formed', $this->integer(1)->defaultValue(0)->notNull());
        Yii::$app->db->createCommand("UPDATE `order` SET `formed` = 1 WHERE id > 0")->execute();

        $this->dropColumn('order_user', 'send');

        $this->addColumn('order_user', 'user_updated', $this->integer(11)->defaultValue(0)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('order_user', 'send', $this->integer(1)->defaultValue(0)->notNull());
        $this->dropColumn('order', 'formed');

        $this->dropColumn('order_user', 'user_updated');
    }
}
