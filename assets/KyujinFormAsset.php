<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/09
 * Time: 21:28
 */

namespace app\assets;


use yii\web\AssetBundle;

class KyujinFormAsset extends AssetBundle
{
    public $sourcePath = '@webroot/kyujin-yii';
    public $js = [
        'yii.activeForm.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}