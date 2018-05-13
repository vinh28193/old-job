<?php
namespace app\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * FlatUiのSwitchCheckboxのアセット
 */
class SwitchCheckboxAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        // 初期化
        $initJs = <<<JS
        $('[data-toggle="switch"]').bootstrapSwitch();
JS;
        $view->registerJs($initJs);

        // input要素の親labelのクラスを削除
        $deleteJs = <<<JS
        $(":checkbox").parent().removeClass("checkbox");
JS;
        $view->registerJs($deleteJs, View::POS_LOAD);

        return parent::registerAssetFiles($view);
    }
}
