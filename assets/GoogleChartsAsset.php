<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * Googleチャートのアセット
 */
class GoogleChartsAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/google/loader.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
