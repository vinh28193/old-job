<?php
namespace app\assets;

use yii\web\AssetBundle;

/**
 * 応募画面のアセット
 * @author Yukinori Nakamura <y_nakamura@id-frontier.jp>
 */
class AutoHeightAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/jQueryAutoHeight.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
