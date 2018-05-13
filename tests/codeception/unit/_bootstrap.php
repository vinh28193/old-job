<?php
require_once '../vendor/autoload.php';
require_once '../vendor/yiisoft/yii2/Yii.php';

$config = require('./codeception/config/unit.php');

(new yii\web\Application($config));