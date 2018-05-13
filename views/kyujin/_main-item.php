<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 */

use app\models\manage\BaseColumnSet;
use app\models\manage\JobMaster;
use app\models\manage\MainDisp;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model JobMaster */
/* @var $mainDisps \app\models\manage\JobColumnSet[] */
/* @var $mainDispName string */

if (isset($mainDisps[$mainDispName])) {
    if ($mainDisps[$mainDispName]->data_type == BaseColumnSet::DATA_TYPE_URL) {
        /** @var \app\common\ProseedsFormatter $formatter */
        $formatter = Yii::$app->formatter;
        echo $formatter->asNewWindowUrl($model->{$mainDisps[$mainDispName]->column_name});
    } else {
        $tagInfo = ArrayHelper::getValue(MainDisp::TAG_INFO, $mainDispName);
        echo Html::tag($tagInfo['tag'], nl2br(Html::encode($model->{$mainDisps[$mainDispName]->column_name})), $tagInfo['options']);
    }
}