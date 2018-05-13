<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/11/21
 * Time: 14:22
 */

namespace app\assets;

use yii\web\AssetBundle;

class DynamicFormAsset extends \wbraganca\dynamicform\DynamicFormAsset
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/../web/js/dynamic-form');
        $this->setupAssets('js', ['yii2-dynamic-form']);
        AssetBundle::init();
    }
}
