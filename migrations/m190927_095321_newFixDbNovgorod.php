<?php

use yii\db\Migration;

/**
 * Class m190927_095321_newFixDbNovgorod
 */
class m190927_095321_newFixDbNovgorod extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $arr = [
            [
                'name' => 'ТСНБ ТЕР-2001 Новгородской области',
                'cost' => 100
            ],
        ];

        foreach ($arr as $item) {
            $this->insert('database', [
                'name' => $item['name'],
                'cost' => $item['cost']
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('database', ['name' => 'ТСНБ ТЕР-2001 Новгородской области']);
    }
}
