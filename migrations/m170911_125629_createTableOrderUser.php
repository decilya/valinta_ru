<?php

use yii\db\Migration;

class m170911_125629_createTableOrderUser extends Migration
{
	public function safeUp()
	{
		$this->createTable('order_user', [
			'id' => 'pk',
			'order_id' => 'int NOT NULL',
			'user_id' => 'int NOT NULL',
			'sent' => 'tinyint(1) DEFAULT \'0\''
		], 'ENGINE=InnoDB DEFAULT CHARSET=utf8');
	}

	public function safeDown()
	{
		$this->dropTable('order_user');
	}
}
