<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/04/22
 * Time: 16:33
 */

use app\models\manage\JobMaster;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $mainDisps array */
/* @var $model JobMaster */
/* @var $picId integer */

if (isset($mainDisps['pic' . $picId])) {
    echo Html::tag('div',
        Html::img($model->getJobImagePath($picId) ?: $model::NO_IMAGE_PATH, ['id' => $mainDisps['pic' . $picId]->column_name]),
        ['class' => 'mod-excerptBox__photo', 'style' => 'cursor:pointer']);
}
