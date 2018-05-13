<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * 応募画面のアセット
 * @author Yukinori Nakamura <y_nakamura@id-frontier.jp>
 */
class ApplyAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/requireditem.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\MainAsset',
    ];
}
