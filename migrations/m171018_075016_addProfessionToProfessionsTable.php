<?php

use yii\db\Migration;

class m171018_075016_addProfessionToProfessionsTable extends Migration
{
    public function safeUp()
    {
		$this->insert('professions', [
			'title' => 'Монтаж технологических трубопроводов и оборудования'
		]);
    }

    public function safeDown()
    {
		$this->delete('professions', [
			'title' => 'Монтаж технологических трубопроводов и оборудования'
		]);
    }

}
