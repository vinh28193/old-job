<?php
use yii\helpers\Html;
/**
 * 入力フロー
 * @var $this \yii\web\View
 * @var $currentClass array
 */
?>
<div id="step_bar_box">
    <ol class="step_bar">
        <?= Html::tag('li', Yii::t('app', '入力' ), ['class' => isset($currentClass['index']) ? 'current' : '']) ?>
        <?= Html::tag('li', Yii::t('app', '確認' ), ['class' => isset($currentClass['confirm']) ? 'current' : '']) ?>
        <?= Html::tag('li', Yii::t('app', '完了' ), ['class' => isset($currentClass['complete']) ? 'current' : '']) ?>
    </ol>
</div>