<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',

    'on afterOpen' => function($event) {
        $event->sender->createCommand("SET sql_mode = ''")->execute();
    }
];
