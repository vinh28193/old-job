<?php

namespace app\common\Helper;


use yii;
use yii\helpers\Url;

class Html extends yii\bootstrap\Html
{
    /**
     * ポップアップを表示させるボタンを生成
     * @param $label string ボタンのラベル
     * @param $url string 表示するURL
     * @param array $divOptions 囲むdivタグのoption
     * @param $btnClass string buttonのクラス
     * @return string html
     */
    public static function popUpHelpButton($label, $url, $divOptions = ['style' => 'text-align:right; margin: 5px 0px'], $btnClass = 'btn btn-simple')
    {
        $label = Html::icon("question-sign") . $label;
        $content = Html::a($label, "#", ['class' => $btnClass, 'onclick' => "javascript:window.open('" . Url::to([$url]) ."', '_blank', 'width=700,height=800,scrollbars=1,resizable=1')"]);
        return Html::tag("div", $content, $divOptions);
    }
}