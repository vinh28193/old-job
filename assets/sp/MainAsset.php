<?php
namespace app\assets\sp;

use Yii;
use yii\web\AssetBundle;

/**
 * Class MainAsset SP画面向けメイン Asset
 *
 * @package app\assets\sp
 */
class MainAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $basePath = '@webroot';
    /**
     * @var string
     */
    public $baseUrl = '@web';
    /**
     * @var array
     */
    public $css = [
        '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css',
        'css/main.css',
    ];
    /**
     * @var array
     */
    public $js = [
        'js/bootstrap.offcanvas.min.js',
        'js/sp/module.js',
    ];
    /**
     * @var array
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->css = array_merge($this->css, [
            '/systemdata/css/color.css',
            'css/sp/style.css',
        ]);
    }
}
