<?php

use yii\db\Migration;

class m170911_130128_createTableUserHasProfessions extends Migration
{
	public function safeUp()
	{
		$this->createTable('user_has_professions', [
			'id' => 'pk',
			'user_id' => 'int DEFAULT NULL',
			'profession_id' => 'int DEFAULT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

		$this->createIndex('user_id_index', 'user_has_professions', 'user_id');
	}

	public function safeDown()
	{
		$this->dropTable('user_has_professions');
	}
}
