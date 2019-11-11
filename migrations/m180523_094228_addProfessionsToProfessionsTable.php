<?php

use yii\db\Migration;

/**
 * Class m180523_094228_addProfessionsToProfessionsTable
 */
class m180523_094228_addProfessionsToProfessionsTable extends Migration
{
	public function safeUp()
	{
		$this->insert('professions', [
			'title' => 'Гидротехнические работы (молы, причалы)'
		]);
		$this->insert('professions', [
			'title' => 'Осушение территорий'
		]);
		$this->insert('professions', [
			'title' => 'Проект организации строительства'
		]);
	}

	public function safeDown()
	{
		$this->delete('professions', [
			'title' => 'Гидротехнические работы (молы, причалы)'
		]);
		$this->delete('professions', [
			'title' => 'Осушение территорий'
		]);
		$this->delete('professions', [
			'title' => 'Проект организации строительства'
		]);
	}
}
