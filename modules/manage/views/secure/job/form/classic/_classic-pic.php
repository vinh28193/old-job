<?php
use app\models\manage\JobMaster;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $tableForm \proseeds\widgets\TableForm */
/* @var $model JobMaster */
/* @var $mainDisp string */
/* @var $picId integer */


if (isset($mainDisp['pic' . $picId])) {
    echo $tableForm->row($model, 'media_upload_id_' . $picId)
        ->layout(function () use ($tableForm, $model, $mainDisp, $picId) {
            echo Html::img($model->getJobImagePath($picId) ?: JobMaster::NO_IMAGE_PATH, ['id' => $mainDisp['pic' . $picId]->column_name, 'class' => 'select-img']);
            echo $tableForm->form($model, 'media_upload_id_' . $picId)->hiddenInput();
    });
}
