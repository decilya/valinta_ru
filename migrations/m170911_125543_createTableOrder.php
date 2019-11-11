<?php

use yii\db\Migration;

class m170911_125543_createTableOrder extends Migration
{

    public function safeUp()
    {
		$this->createTable('order', [
			'id' => 'pk',
			'user_id' => 'int NOT NULL',
			'user_change_id' => 'int DEFAULT NULL',
			'created_at' => 'int NOT NULL',
			'updated_at' => 'int DEFAULT NULL',
			'finished_at' => 'int NOT NULL',
			'name' => 'string NOT NULL',
			'fio' => 'string NOT NULL',
			'phone' => 'string NOT NULL',
			'email' => 'string NOT NULL',
			'price' => 'int(10) NOT NULL DEFAULT \'0\'',
			'text' => 'text NOT NULL',
			'link' => 'string NOT NULL',
			'published' => 'tinyint(1) DEFAULT \'1\'',
			'checked' => 'tinyint(1) DEFAULT \'0\'',
			'closing_reason' => 'int(1) DEFAULT NULL',
			'closing_reason_text' => 'string DEFAULT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }

    public function safeDown()
    {
		$this->dropTable('order');
	}
}
