<?php

use yii\db\Migration;

class m170911_130037_createTableReports extends Migration
{
	public function safeUp()
	{
		$this->createTable('reports', [
			'id' => 'pk',
			'user_id' => 'int NOT NULL',
			'date' => 'datetime NOT NULL',
			'ip' => 'string DEFAULT NULL',
			'day_index' => 'int NOT NULL',
			'week_index' => 'int NOT NULL',
			'month_index' => 'int NOT NULL',
			'year' => 'int NOT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
	}

	public function safeDown()
	{
		$this->dropTable('reports');
	}
}
