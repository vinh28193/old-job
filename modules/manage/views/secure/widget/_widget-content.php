<?php
use app\models\manage\Widget;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * @var $widget Widget
 * @var $widgetSettingLink String
 */
$widgetSettingLink = Html::a(
    Yii::t('app', '設定'),
    Url::to(['pjax-modal', 'id' => $widget->id]),
    ['class' => 'pjaxModal text-right', 'title' => Yii::t('app', '変更')]
);
?>
<div class="row">
    <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
        <p id='item-<?= $widget->id ?>' style="word-wrap: break-word;"><?= Html::encode($widget->widget_name) ?></p>
        <div class='text-right'>
            <?= $widgetSettingLink ?>
        </div>
    </div>