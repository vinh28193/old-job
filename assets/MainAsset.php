<?php
namespace app\assets;

use Yii;
use yii\helpers\Url;
use yii\web\AssetBundle;

/**
 * メインのアセット
 * @author Yukinori Nakamura <y_nakamura@id-frontier.jp>
 */
class MainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/main.css',
    ];
    public $js = [
        'js/bootstrap.offcanvas.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        parent::init();
        $url = '/systemdata/css/color.css?public=1';
        $this->css = array_merge($this->css, [$url, '/css/pc/style.css']);
    }
}
