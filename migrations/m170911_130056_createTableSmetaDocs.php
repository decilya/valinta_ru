<?php

use yii\db\Migration;

class m170911_130056_createTableSmetaDocs extends Migration
{
	public function safeUp()
	{
		$this->createTable('smeta_docs', [
			'id' => 'pk',
			'title' => 'string NOT NULL',
		], 'ENGINE=MyISAM DEFAULT CHARSET=utf8');

			$arr = [
				'Локальная смета', 'Сводный сметный расчет/Объектная смета', 'Акт КС-2', 'Форма КС-3', 'Форма КС-6а', 'Проверка смет/актов', 'Экспертиза смет', 'Тендерная документация'
			];

		foreach($arr as $item){

			$this->insert('smeta_docs', [
				'title' => $item
			]);

		}
	}

	public function safeDown()
	{
		$this->dropTable('smeta_docs');
	}
}
