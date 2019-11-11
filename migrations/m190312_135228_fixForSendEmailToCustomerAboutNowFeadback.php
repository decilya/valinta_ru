<?php

use yii\db\Migration;

class m190312_135228_fixForSendEmailToCustomerAboutNowFeadback extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('order_user', 'sent', 'send');

        $this->addColumn('order_feadback_user', 'send', $this->integer(1)->defaultValue(0)->notNull());

        $this->addColumn('users', 'real_id', $this->integer(11)->defaultValue(0)->null());

        $this->addColumn('order_feadback_user', 'created_at', $this->integer(11)->null());
    }

    public function safeDown()
    {
        $this->renameColumn('order_user', 'send', 'sent');

        $this->dropColumn('order_feadback_user', 'send');

        $this->dropColumn('users', 'real_id');

        $this->dropColumn('order_feadback_user', 'created_at');
    }
}
