<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * AjaxZip3Assetのアセット
 */
class AjaxZip3Asset extends AssetBundle
{
    public $js = [
        'https://ajaxzip3.github.io/ajaxzip3.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\MainAsset',
    ];
}
