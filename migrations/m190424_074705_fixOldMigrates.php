<?php

use yii\db\Migration;

class m190424_074705_fixOldMigrates extends Migration
{
    public function safeUp()
    {
        $this->dropTable('user_review_order');

        $this->createIndex(
            'idx-review-user_id',
            'order_feadback_user',
            'user_id'
        );

        $this->createIndex(
            'idx-review-order_id',
            'order_feadback_user',
            'order_id'
        );

        $this->createIndex(
            'idx-review-customer_id',
            'order_feadback_user',
            'customer_id'
        );


        /////////////////

        $this->createIndex(
            'idx-show-customer_id',
            'show_user_contacts_counter',
            'customer_id'
        );

        $this->createIndex(
            'idx-show-user_id',
            'show_user_contacts_counter',
            'user_id'
        );


        /////////////////


        $this->createIndex(
            'idx-order-auth_id',
            'order',
            'auth_id'
        );

        /////////////////////////////////////

        $this->createIndex(
            'idx-users-real_id',
            'users',
            'real_id'
        );

        /////////////////////

        $this->createIndex(
            'idx-customer-real_id',
            'customer',
            'real_id'
        );

    }

    public function safeDown()
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
            , $tableOptions
        );


        $this->dropIndex(
            'idx-review-user_id',
            'order_feadback_user'
        );

        $this->dropIndex(
            'idx-review-order_id',
            'order_feadback_user'
        );

        $this->dropIndex(
            'idx-review-customer_id',
            'order_feadback_user'
        );


        /////////////////

        $this->dropIndex(
            'idx-show-customer_id',
            'show_user_contacts_counter'
        );

        $this->dropIndex(
            'idx-show-user_id',
            'show_user_contacts_counter'
        );


        /////////////////


        $this->dropIndex(
            'idx-order-auth_id',
            'order'
        );

        /////////////////////////////////////

        $this->dropIndex(
            'idx-users-real_id',
            'users'
        );

        /////////////////////

        $this->dropIndex(
            'idx-customer-real_id',
            'customer'
        );
    }
}