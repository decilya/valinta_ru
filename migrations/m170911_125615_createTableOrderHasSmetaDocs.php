<?php

use yii\db\Migration;

class m170911_125615_createTableOrderHasSmetaDocs extends Migration
{
	public function safeUp()
	{
		$this->createTable('order_has_smeta_docs', [
			'id' => 'pk',
			'order_id' => 'int NOT NULL',
			'smeta_docs_id' => 'int NOT NULL'
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
	}

	public function safeDown()
	{
		$this->dropTable('order_has_smeta_docs');

	}
}
