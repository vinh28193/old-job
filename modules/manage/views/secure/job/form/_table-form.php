<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/01/25
 * Time: 13:32
 */
use app\common\PostablePjax;
use proseeds\widgets\TableForm;

/* @var $this yii\web\View */
/* @var $id integer */
/* @var $viewName string */
/* @var $pjaxId string */
/* @var $model app\models\manage\JobMaster */
/* @var $dispTypeId integer */



$tableForm = TableForm::begin([
    'options' => ['enctype' => 'multipart/form-data', 'id'=>'form'],
    'tableOptions' => ['class' => 'table table-bordered'],
]);
PostablePjax::begin(['id' => $pjaxId]);
echo $this->render($viewName, [
    'id' => $id,
    'model' => $model,
    'dispTypeId' => $dispTypeId ? : null,
    'tableForm' => $tableForm,
]);
PostablePjax::end();
TableForm::end();
