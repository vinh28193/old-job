<?php

use yii\bootstrap\Html;
use app\models\manage\JobMaster;

if (!isset($cleaUrl)) {
    $cleaUrl = '';
}

$saveOnly = '';
$submitButtonMsg = '';
// 審査機能ON かつ 求人原稿登録・編集画面 かつ 運営元管理者以外の場合、一時保存ボタンを表示し、ボタンメッセージを変える
if (Yii::$app->tenant->tenant->review_use && $model instanceof JobMaster && !Yii::$app->user->identity->isOwner()) {
    $saveOnly = Html::submitButton(
        Html::icon('pencil') . Yii::t('app', '一時保存する'),
        [
            'class' => 'btn btn-primary btn-lg w100s w50m mgt10 mgl20',
            'name' => 'saveOnly',
            'onclick' => 'document.getElementById("submitType").value = "saveOnly";',
        ]
    );
    $baseMsg = '{action}し、審査依頼へ進む';
    $action = $model->isNewRecord ? Yii::t('app', '登録') : Yii::t('app', '変更');
    $submitButtonMsg = Yii::t('app', $baseMsg, ['action' => $action]);
} else {
    $submitButtonMsg = $model->isNewRecord ? Yii::t('app', '登録する') : Yii::t('app', '変更する');
}
?>

<div class="form-group" style="text-align: center">
    <?php
    echo Html::a(Yii::t('app', 'クリア'), $cleaUrl, ['class' => 'btn btn-simple mgt10']);
    echo $saveOnly;
    echo Html::submitButton(
        Html::icon('pencil') . $submitButtonMsg,
        [
            'class' => 'btn btn-primary btn-lg w100s w50m w50l mgt10 mgl20',
            'name' => 'complete',
        ]
    );
    ?>
</div>
