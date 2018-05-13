<?php

namespace app\assets;

use yii\web\AssetBundle;

/**
 * 画面遷移確認ボックスのアセット
 * @author Yukinori Nakamura <y_nakamura@id-frontier.jp>
 */
class ConfirmBoxAsset extends AssetBundle
{
    /**
     * 画面遷移確認ボックス表示
     * @param \yii\web\View $view ビューヘルパー
     * @param string $message 確認メッセージ
     * @param string $selector 対象範囲のセレクタ
     */
    public static function transitionConfirmBox($view, $message = '', $selector = 'form')
    {
        $js = <<< JS
var changed = true;
$('$selector').submit(function () {
  changed = false;
});

$(window).on('beforeunload', function () {
  if (changed) {
    return '$message';
  }
});
                
$("$selector input, $selector select, $selector textarea").change(function () {
  changed = true;
});
JS;
        $view->registerJs($js);
    }

}
