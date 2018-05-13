<?php
use proseeds\widgets\GridButtonBar;
use proseeds\widgets\GridSubmitButton;
use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Html;
use yii\helpers\Url;

// todo gridのidが'grid_id'じゃないと正常に動かないのでそこの連携が取れるように見直し

/* @var $pagename string */
/* @var $buttons array */
/* @var $additionalDeleteConfirmComment string */
/* @var $count int */

if (!isset($additionalDeleteConfirmComment)) {
    $additionalDeleteConfirmComment = '';
}

// todo 突貫なので要リファクタリング
if ($count) {
    $disabled = '';
} else {
    $disabled = ' disabled';
}

GridButtonBar::begin();

if (isset($buttons['add'])) {
    echo Html::beginTag('li');
    echo Html::a('<i class="glyphicon glyphicon-plus"></i>' . $pagename . Yii::t('app', 'を登録する'), Url::toRoute('create'), ['class' => 'btn btn-danger btn-sm']);
    echo Html::endTag('li');
}

if (isset($buttons['addModal'])) {
    echo Html::beginTag('li');
    echo Html::a('<i class="glyphicon glyphicon-plus"></i>' . $pagename . Yii::t('app', 'を登録する'), Url::toRoute('pjax-modal'), ['class' => 'btn btn-danger btn-sm pjaxModal']);
    echo Html::endTag('li');
}

if (isset($buttons['csvEdit'])) {
    echo Html::beginTag('li');
    echo Html::a('<i class="glyphicon glyphicon-plus"></i>' . Yii::t('app', 'CSV一括登録・変更する'), Url::toRoute('csv'), ['class' => 'btn btn-danger btn-sm']);
    echo Html::endTag('li');
}

if (isset($buttons['csv'])) {
    echo Html::beginTag('li');
    echo Html::tag('div', GridSubmitButton::widget([
        'text' => Html::icon('download-alt') . Yii::t('app', 'CSVダウンロード'),
        'tag' => 'button',
        'options' => ['tabindex' => -1, 'class' => 'btn btn-simple btn-sm dropdown-toggle' . $disabled],
        'url' => 'csv-download',
        'gridSelector' => '#grid_id',
    ]), ['class' => 'btn-group']);
    echo Html::endTag('li');
}

if (isset($buttons['csvPlan'])) {
    echo Html::beginTag('li');
    echo ButtonDropdown::widget([
        'label' => Html::icon('download-alt') . Yii::t('app', 'CSVダウンロード'),
        'encodeLabel' => false,
        'clientOptions' => false,
        'options' => [
            'type' => 'button',
            'class' => 'btn btn-simple btn-sm' . $disabled,
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false',
        ],
        'dropdown' => [
            'items' => [
                Html::tag('li', GridSubmitButton::widget([
                    'text' => Yii::t('app', '一覧'),
                    'tag' => 'a',
                    'options' => ['tabindex' => -1, 'href' => '#'],
                    'url' => 'csv-download',
                    'gridSelector' => '#grid_id',
                ])),
                Html::tag('li', GridSubmitButton::widget([
                    'text' => Yii::t('app', '申込みプラン'),
                    'tag' => 'a',
                    'options' => ['tabindex' => -2, 'href' => '#'],
                    'url' => 'plan-csv-download',
                    'gridSelector' => '#grid_id',
                ])),
            ],
        ],
    ]);
    echo Html::endTag('li');
}

if (isset($buttons['delete'])) {
    echo Html::tag('li', GridSubmitButton::widget([
        'method' => 'POST',
        'text' => Html::icon('remove') . Yii::t('app', 'まとめて削除する'),
        'tag' => 'button',
        'options' => ['class' => 'btn btn-simple btn-sm', 'disabled' => $count ? false : true],
        'url' => 'delete',
        'gridSelector' => '#grid_id',
        'confirmMessage' => Yii::t('app', '削除したものは元に戻せません。削除しますか？') . $additionalDeleteConfirmComment,
    ]));
}

GridButtonBar::end();
