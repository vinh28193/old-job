<?php
/**
 * Created by PhpStorm.
 * User: T.Hosaka
 * Date: 2016/05/06
 * Time: 16:33
 */

namespace app\assets;


use yii\web\AssetBundle;

class SlickSliderAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        'js/job/slick.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}