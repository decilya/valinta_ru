<?php

use yii\db\Migration;

class m170911_130119_createTableUserHasNormativeBases extends Migration
{
	public function safeUp()
	{
		$this->createTable('user_has_normative_bases', [
			'id' => 'pk',
			'user_id' => 'int DEFAULT NULL',
			'normative_bases_id' => 'int DEFAULT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

		$this->createIndex('user_id_index', 'user_has_normative_bases', 'user_id');
	}

	public function safeDown()
	{
		$this->dropTable('user_has_normative_bases');
	}
}
