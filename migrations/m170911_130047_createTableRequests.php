<?php

use yii\db\Migration;

class m170911_130047_createTableRequests extends Migration
{
	public function safeUp()
	{
		$this->createTable('requests', [
			'id' => 'pk',
			'fio' => 'string NOT NULL',
			'email' => 'string NOT NULL',
			'phone' => 'string(20) NOT NULL',
			'date_created' => 'int NOT NULL',
			'status_value' => 'int(1) NOT NULL DEFAULT \'1\'',
			'comment' => 'longtext',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
	}

	public function safeDown()
	{
		$this->dropTable('requests');
	}
}
