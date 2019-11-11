<?php

use yii\db\Migration;

/**
 * Class m190827_152124_fixNewDatabese
 */
class m190827_152124_fixNewDatabese extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $arr = [
            [
                'name' => 'ТСНБ ТЕР-2001 Республики Карелия',
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
        $this->delete('database', ['name' => 'ТСНБ ТЕР-2001 Республики Карелия']);
    }

}
