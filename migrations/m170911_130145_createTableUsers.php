<?php

use yii\db\Migration;

class m170911_130145_createTableUsers extends Migration
{
	public function safeUp()
	{
		$this->createTable('users', [
			'id' => 'pk',
			'fio' => 'string(100) DEFAULT NULL',
			'email' => 'string(129) DEFAULT NULL',
			'phone' => 'string(20) DEFAULT NULL',
			'experience' => 'longtext',
			'has_city' => 'int NOT NULL DEFAULT \'0\'',
			'city_id' => 'int DEFAULT NULL',
			'has_price' => 'int NOT NULL DEFAULT \'0\'',
			'price' => 'int DEFAULT NULL',
			'ipap_attestat_id' => 'string(15) DEFAULT NULL',
			'status_id' => 'int DEFAULT \'1\'',
			'is_visible' => 'tinyint(4) NOT NULL DEFAULT \'1\'',
			'reject_msg' => 'longtext',
			'date_created' => 'int NOT NULL',
			'date_changed' => 'int DEFAULT NULL',
			'last_change_by_user' => 'tinyint(4) DEFAULT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

		$this->createIndex('id_index', 'users', 'id');

	}

	public function safeDown()
	{
		$this->dropTable('users');
	}
}
