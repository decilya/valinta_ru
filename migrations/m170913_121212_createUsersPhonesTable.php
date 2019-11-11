<?php

use yii\db\Migration;

class m170913_121212_createUsersPhonesTable extends Migration
{
    public function safeUp()
    {
		$this->createTable('users_phones', [
			'id' => 'pk',
			'user_id' => 'int NOT NULL',
			'phone_id' => 'int NOT NULL',
			'index' => 'tinyint(2) NOT NULL',
			'is_new' => 'tinyint(1) NOT NULL DEFAULT 1',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }

    public function safeDown()
    {
		$this->dropTable('users_phones');
    }
}
