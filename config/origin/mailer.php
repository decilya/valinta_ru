<?php

return [
    'class' => 'yii\swiftmailer\Mailer',
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => '127.0.0.1',
        'port' => '25',
    ],
    'useFileTransport' => false,
];