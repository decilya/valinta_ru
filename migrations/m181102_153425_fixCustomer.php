<?php

use yii\db\Migration;

class m181102_153425_fixCustomer extends Migration
{
    public function safeUp()
    {
        $sql = "ALTER TABLE auth ENGINE=InnoDB";
        $this->execute($sql);

        // 1 - Требует проверки
        $this->addColumn('customer', 'status_id', $this->integer(1)->defaultValue(1)->notNull());
        // начнем не с 0 (о Боже (лол)), чтобы потом не схватить нигде ошибки из-за приведения типов... ну вдруг... чВ

        $this->addColumn('customer', 'updated_at', $this->integer(11)->defaultValue(null)->null());
        $this->addColumn('customer', 'real_id', $this->integer(11)->defaultValue(0)->null());

        $this->createIndex('idx-auth-id', 'auth', 'customer_id');
//        $this->addForeignKey('fk-prod-auth-customer', 'auth', 'customer_id', 'customer', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        // drops foreign key for table `customer`
//        $this->dropForeignKey(
//            'fk-prod-auth-customer',
//            'auth'
//        );
//
        $this->dropIndex(
            'idx-auth-id',
            'auth'
        );

        $this->dropColumn('customer', 'status_id');
        $this->dropColumn('customer', 'updated_at');
        $this->dropColumn('customer', 'real_id');
    }

}
