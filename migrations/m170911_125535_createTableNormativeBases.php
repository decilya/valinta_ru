<?php

use yii\db\Migration;

class m170911_125535_createTableNormativeBases extends Migration
{
    public function safeUp()
    {
		$this->createTable('normative_bases', [
			'id' => 'pk',
			'title' => 'string NOT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

		$arr = [
			'ГЭСН',
			'ФЕР-2001',
			'ТЕР-2001',
			'ПИР',
			'ПНР',
			'ТСН-2001 Москва',
			'ТСНБ-2001',
			'Ведомственные',
			'Индивидуальные/фирменные',
			'Госэталон',
			'Прочее',
		];

		foreach($arr as $item){

			$this->insert('normative_bases', [
				'title' => $item
			]);

		}

    }

    public function safeDown()
    {
		$this->dropTable('normative_bases');
    }
}
