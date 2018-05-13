<?php

use yii\widgets\ListView;

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $hotJob \app\models\manage\HotJob */

$layout = '
<div class="widgetlayout widgetlayout2-1">
    <div class="widget hotJobLayout box-pc-4 box-sp-1 style-pc-1 style-sp-2">
        <h2>{summary}</h2>
        <div class="widget-inner">{items}</div>
    </div>
</div>
';

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'summary' => Yii::t('app', $hotJob->title),
    'layout' => $layout,
    'itemView' => '_hot-job-itemview',
    'itemOptions' => ['class' => 'widget-data'],
    'emptyText' => '',
    'viewParams' => ['hotJob' => $hotJob],
]);

