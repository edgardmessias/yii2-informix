<?php

/**
 * This is the configuration file for the Yii2 unit tests.
 * You can override configuration values by creating a `config.local.php` file
 * and manipulate the `$config` variable.
 */
$config = [
    'databases' => [
        'informix' => [
            'class'    => '\edgardmessias\db\informix\Connection',
            'dsn'      => 'informix:host=127.0.0.1;service=9088;database=test;server=dev;protocol=onsoctcp;CLIENT_LOCALE=en_US.utf8;DB_LOCALE=en_US.utf8;EnableScrollableCursors=1',
            'username' => 'test',
            'password' => 'test',
            'fixture'  => __DIR__ . '/source.sql',
        ]
    ],
];

if (is_file(__DIR__ . '/config.local.php')) {
    include __DIR__ . '/config.local.php';
}

return $config;
