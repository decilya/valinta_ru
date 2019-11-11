<?php

use yii\db\Migration;

/**
 * Class m190924_084417_newFiDbSbp
 */
class m190924_084417_newFiDbSbp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $arr = [
            [
                'name' => 'ТСНБ ТЕР-2001 Санкт-Петербург (КГЗ СПб)',
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
        $this->delete('database', ['name' => 'ТСНБ ТЕР-2001 Санкт-Петербург (КГЗ СПб)']);
    }

}
