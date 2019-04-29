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
            'dsn'      => 'informix:host=localhost;service=9088;database=test;server=informix;protocol=onsoctcp;CLIENT_LOCALE=en_US.utf8;DB_LOCALE=en_US.utf8;EnableScrollableCursors=1;CursorBehavior=1;DELIMIDENT=y',
            'username' => 'informix',
            'password' => 'in4mix',
            'fixture'  => __DIR__ . '/source.sql',
        ]
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
];

if (is_file(__DIR__ . '/config.local.php')) {
    include __DIR__ . '/config.local.php';
}

return $config;
