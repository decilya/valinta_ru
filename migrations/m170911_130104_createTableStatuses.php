<?php

use yii\db\Migration;

class m170911_130104_createTableStatuses extends Migration
{
	public function safeUp()
	{
		$this->createTable('statuses', [
			'id' => 'pk',
			'title' => 'string(45) NOT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

		$this->insert('statuses', [
			'id' => 1,
			'title' => 'требует проверки'
		]);

		$this->insert('statuses', [
			'id' => 2,
			'title' => 'подтверждена'
		]);

		$this->insert('statuses', [
			'id' => 3,
			'title' => 'отклонена'
		]);
	}

	public function safeDown()
	{
		$this->dropTable('statuses');
	}
}
