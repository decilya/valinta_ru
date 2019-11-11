<?php

use yii\db\Migration;

class m170911_125606_createTableOrderHasProfessions extends Migration
{
	public function safeUp()
	{
		$this->createTable('order_has_professions', [
			'id' => 'pk',
			'order_id' => 'int NOT NULL',
			'professions_id' => 'int NOT NULL'
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
	}

	public function safeDown()
	{
		$this->dropTable('order_has_professions');

	}
}
