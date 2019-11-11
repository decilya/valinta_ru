<?php

use yii\db\Migration;

class m170911_124315_createTableAuth extends Migration
{
    public function safeUp()
    {
		$this->createTable('auth', [
			'id' => 'pk',
			'login' => 'string DEFAULT NULL',
			'password' => 'string DEFAULT NULL',
			'last_auth' => 'int DEFAULT NULL',
			'is_admin' => 'tinyint(4) DEFAULT \'0\'',
			'is_user' => 'tinyint(4) DEFAULT \'1\'',
			'user_id' => 'int DEFAULT NULL',
			'recovery_token' => 'string(50) DEFAULT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

		$this->insert('auth', [
			'login' => 'admin',
			'password' => Yii::$app->security->generatePasswordHash('1qwe2qaz'),
			'is_admin' => 1,
			'is_user' => 0,
			'user_id' => null
		]);
    }

    public function safeDown()
    {
		$this->dropTable('auth');
    }
}
