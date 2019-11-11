<?php

use yii\db\Migration;

class m170911_125558_createTableOrderHasNormativeBases extends Migration
{
    public function safeUp()
    {
		$this->createTable('order_has_normative_bases', [
			'id' => 'pk',
			'order_id' => 'int NOT NULL',
			'normative_bases_id' => 'int NOT NULL'
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');
    }

    public function safeDown()
    {
		$this->dropTable('order_has_normative_bases');

	}
}
