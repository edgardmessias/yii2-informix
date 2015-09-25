Informix Extension for Yii 2 (yii2-informix)
============================================

This extension adds [Informix](https://www-01.ibm.com/software/data/informix/) database engine extension for the [Yii framework 2.0](http://www.yiiframework.com).

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![Build Status](https://travis-ci.org/edgardmessias/yii2-informix.svg?branch=master)](https://travis-ci.org/edgardmessias/yii2-informix)
[![Total Downloads](https://img.shields.io/packagist/dt/edgardmessias/yii2-informix.svg)](https://packagist.org/packages/edgardmessias/yii2-informix)
[![Dependency Status](https://www.versioneye.com/php/edgardmessias:yii2-informix/dev-master/badge.png)](https://www.versioneye.com/php/edgardmessias:yii2-informix/dev-master)
[![Reference Status](https://www.versioneye.com/php/edgardmessias:yii2-informix/reference_badge.svg)](https://www.versioneye.com/php/edgardmessias:yii2-informix/references)

Requirements
------------
 * Informix Client SDK installed
 * PHP module pdo_informix
 * Informix Database Server 11.50 or greater

Unsupported
------------

Functions not supported by the Informix database:

 * `INSERT`, `UPDATE`, `DELETE` with `READ UNCOMMITTED` transaction

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```bash
php composer.phar require --prefer-dist "edgardmessias/yii2-informix:*"
```

or add

```json
"edgardmessias/yii2-informix": "*"
```

to the require section of your composer.json.


Configuration
-------------

To use this extension, simply add the following code in your application configuration:

```php
return [
    //....
    'components' => [
        'db' => [
            'class'    => 'edgardmessias\db\informix\Connection',
            'dsn'      => 'informix:host=127.0.0.1;service=9088;database=test;server=dev;protocol=onsoctcp;CLIENT_LOCALE=en_US.utf8;DB_LOCALE=en_US.utf8;EnableScrollableCursors=1',
            'username' => 'username',
            'password' => 'password',
        ],
    ],
];
```
