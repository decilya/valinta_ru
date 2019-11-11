<?php

use yii\db\Migration;

/**
 * Class m190812_080101_newFixDatabases
 */
class m190812_080101_newFixDatabases extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('database', ['name' => 'ТСНБ ТЕР-2001 Республики Карелия']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $arr = [
            [
                'name' => 'ТСНБ ТЕР-2001 Республики Карелия',
                'cost' => 100
            ],
        ];

        foreach ($arr as  $item){
            $this->insert('database', [
                'name' => $item['name'],
                'cost' => $item['cost']
            ]);
        }
    }
}
