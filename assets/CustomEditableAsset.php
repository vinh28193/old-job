<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/04/29
 * Time: 16:50
 */

namespace app\assets;

use yii\web\AssetBundle;

class CustomEditableAsset extends AssetBundle
{
    public $basePath = '@webroot/editable';
    public $baseUrl = '@web/editable';
    public $css = [
        'css/bootstrap-editable.css'
    ];
    public $js = [
        'js/bootstrap-editable.js',
        'js/count-for-editable.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}