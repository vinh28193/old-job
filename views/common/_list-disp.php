<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\manage\ListDisp;

//募集要項一覧のビュー
/* @var $model app\models\JobMasterDisp */
/* @var $dispTypeId int */
/* @var $headerMessage string */
$attributes = ListDisp::getJobAttributesWithFormat($model);

if (count($attributes) > 0) {
    echo Html::tag('h2', $headerMessage);
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'options' => [
            'class' => 'table mod-table1',
        ]
    ]);
}