<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\manage\ClientDisp;

//企業情報一覧のビュー
/* @var $model \app\models\manage\JobMaster */
/* @var $dispTypeId int */
/* @var $headerMessage string */
$attributes = ClientDisp::getClientAttributesWithFormat($model);

if (count($attributes) > 0) {
    echo Html::tag('h2', $headerMessage);
    echo DetailView::widget([
        'model' => $model->clientMaster,
        'attributes' => $attributes,
        'options' => [
            'class' => 'table mod-table1',
        ]
    ]);
}
