<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use proseeds\widgets\TableForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $model \app\models\manage\MediaUpload */

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.showModal',
]);
$this->registerJs('$("#modal-' . $model->id . '").modal("show");');
$tableForm = TableForm::begin([
    'id' => 'updateForm-' . $model->id,
    'options' => ['enctype' => 'multipart/form-data'],
    'action' => Url::to(['update', 'id' => $model->id, 'queryParams' => Yii::$app->request->queryParams]),
    'tableOptions' => ['class' => 'table table-bordered'],
]);
Modal::begin([
    'id' => 'modal-' . $model->id,
    'header' => Yii::t('app', 'ファイル変更'),
    'footer' => Html::button(Yii::t('app', '閉じる'), ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal'])
        . ' '
        . Html::submitButton(Yii::t('app', '変更'), ['name' => 'complete', 'class' => 'btn btn-sm btn-primary submitUpdate']),
]);

$tableForm->beginTable();
echo $tableForm->row($model, 'imageFile')->fileInput(['id' => 'fileInput-' . $model->id]);
echo $tableForm->row($model, 'tag')->textInput();
$tableForm->endTable();

Modal::end();
TableForm::end();
Pjax::end();
