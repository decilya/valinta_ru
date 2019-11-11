<?php

use yii\db\Migration;

/**
 * Handles the creation of table `customer_phone`.
 * Has foreign keys to the tables:
 *
 * - `customer`
 */
class m180920_141616_createCustomerPhone extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('customer_phone', [
            'id' => $this->primaryKey(),
            'phone' => $this->string(25)->notNull(),
            'created_at' => $this->integer(12)->null(),
            'customer_id' => $this->integer(11)->notNull(),
        ]);

        // creates index for column `customer_id`
        $this->createIndex(
            'idx-customer_phone-customer_id',
            'customer_phone',
            'customer_id'
        );

        // add foreign key for table `customer`
        $this->addForeignKey(
            'fk-customer_phone-customer_id',
            'customer_phone',
            'customer_id',
            'customer',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        // drops foreign key for table `customer`
        $this->dropForeignKey(
            'fk-customer_phone-customer_id',
            'customer_phone'
        );

        // drops index for column `customer_id`
        $this->dropIndex(
            'idx-customer_phone-customer_id',
            'customer_phone'
        );

        $this->dropTable('customer_phone');
    }
}
