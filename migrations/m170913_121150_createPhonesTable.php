<?php

use yii\db\Migration;

class m170913_121150_createPhonesTable extends Migration
{
    public function safeUp()
    {
		$this->createTable('phones', [
			'id' => 'pk',
			'number' => 'string(20) NOT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

		$this->createIndex('phoneIndex', 'phones', 'number', true);
    }

    public function safeDown()
    {
		$this->dropTable('phones');
    }
}
