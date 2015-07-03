<?php

// ensure we get report on all possible php errors
error_reporting(-1);

define('YII_ENABLE_ERROR_HANDLER', false);
define('YII_DEBUG', true);
$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('@yiiunit', __DIR__ . '/../vendor/yiisoft/yii2-dev/tests/unit');
Yii::setAlias('@edgardmessias/unit/db/informix', __DIR__);
Yii::setAlias('@edgardmessias/db/informix', dirname(__DIR__));
