<?php

use yii\db\Migration;

class m170911_130137_createTableUserHasSmetaDocs extends Migration
{
	public function safeUp()
	{
		$this->createTable('user_has_smeta_docs', [
			'id' => 'pk',
			'user_id' => 'int DEFAULT NULL',
			'smeta_docs_id' => 'int DEFAULT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

		$this->createIndex('user_id_index', 'user_has_smeta_docs', 'user_id');
	}

	public function safeDown()
	{
		$this->dropTable('user_has_smeta_docs');
	}
}
