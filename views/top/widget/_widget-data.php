<?php
//ウィジェット単体の出力
use app\models\manage\Widget;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $widgetType int */
/* @var $element int */
/* @var $widgetData \app\models\manage\WidgetData */

// このエリアで呼び出されているwidgetDataAreaは一つのはずなのでそれを取り出して扱う
$widgetDataArea = $widgetData->widgetDataArea[0] ?? null;
?>
<div class="widget-data">
    <?php
    $url = $widgetDataArea->url ?? null;
    if ($url) {
        echo Html::beginTag('a', ['href' => $url]);
    } else {
        echo Html::beginTag('div', ['class' => 'no-link']);
    }
    for ($i = 1; $i <= Widget::NUMBER_OF_ELEMENTS; $i++) {
        $columnName = "element{$i}";
        $element = $widgetData->widget->$columnName;
        switch ($element) {
            case Widget::ELEMENT_PICT:
                if ($widgetData->widget->is_slider == Widget::IS_SLIDER) {
                    //スライダー機能を使用時lazy-loadを行う
                    $attribute = 'data-lazy';
                } else {
                    $attribute = 'src';
                }
                echo '<span class="img">' . Html::tag(
                    'img',
                    '',
                    [
                        $attribute => Url::to($widgetData->srcUrl()),
                        'alt' => Html::encode($widgetData->title),
                    ]
                ) . '</span>';
                break;
            case Widget::ELEMENT_TITLE:
                echo '<h3 class="title">' . Html::encode($widgetData->title) . '</h3>';
                break;
            case Widget::ELEMENT_DESCRIPTION:
                echo '<p class="description">' . Html::encode($widgetData->description) . '</p>';
                break;
            case Widget::ELEMENT_MOVIE:
                echo $widgetDataArea->movie_tag ?? '';
                break;
            default:
                break;
        }
    }
    if ($url) {
        echo Html::endTag('a');
    } else {
        echo Html::endTag('div');
    }
    ?>
</div>
