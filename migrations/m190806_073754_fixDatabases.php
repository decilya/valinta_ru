<?php

use yii\db\Migration;

class m190806_073754_fixDatabases extends Migration
{
    public function safeUp()
    {

        $this->delete('database', ['name' => 'ТСНБ ТЕР-2001 Московской области']);

        $arr = [
            [
                'name' => 'ТСН-2001 Москвы (Мосгосэкспертиза)',
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

    public function safeDown()
    {
        $this->delete('database', ['name' => 'ТСН-2001 Москвы (Мосгосэкспертиза)']);

        $arr = [
            [
                'name' => 'ТСНБ ТЕР-2001 Московской области',
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
