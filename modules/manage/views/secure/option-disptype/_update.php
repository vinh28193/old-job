<?php
use yii\bootstrap\Modal;
use yii\helpers\Html;
use proseeds\widgets\TableForm;
use yii\helpers\Url;

/** @var $model app\models\manage\DispType */

$tableForm = TableForm::begin([
    'action' => [Url::to(['update', 'id' => $model->id])],
    'options' => ['enctype' => 'multipart/form-data'],
    'tableOptions' => ['class' => ['table', 'table-bordered']]
]);
$fieldOptions = ['tableHeaderOptions' => ['class' => 'm-column']];

Modal::begin([
    'id' => 'modal-' . $model->id,
    'header' => Yii::t('app', '項目変更'),
    'footer' => Html::button(Yii::t('app', '閉じる'), ['class' => ['btn', 'btn-sm', 'btn-default'], 'data-dismiss' => 'modal'])
        . ' '
        . Html::submitButton(Yii::t('app', '変更'), ['class' => ['btn', 'btn-sm', 'btn-primary', 'submitUpdate']])
]);

$tableForm->beginTable();
echo $tableForm->row($model, 'disp_type_name', $fieldOptions)->textInput(['maxlength' => true]);
echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([1 => '公開', 0 => '非公開']);
$tableForm->endTable();

Modal::end();
TableForm::end();

?>