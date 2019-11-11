<?php

use yii\db\Migration;

class m170913_121220_createOrderPhonesTable extends Migration
{
	public function safeUp()
	{
		$this->createTable('order_phones', [
			'id' => 'pk',
			'order_id' => 'int NOT NULL',
			'phone_id' => 'int NOT NULL',
			'index' => 'tinyint(2) NOT NULL',
			'is_new' => 'tinyint(1) NOT NULL DEFAULT 1',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
	}

	public function safeDown()
	{
		$this->dropTable('order_phones');
	}
}
