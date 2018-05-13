<?php
/**
 * 駅検索オーバーレイ
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchForm JobSearchForm */
/* @var $stationParents array */
/* @var $stations array */

$identifier = 'station'; // オーバーレイ識別子
?>
<!-- 駅用選択フロー ===================================================-->
<div class="s-overlay-wrap-block js-overlay" id="ovl-train">
    <div class="c-title-label op-thin">
        <div class="left">
            <h2 class="title"><?= Yii::t('app', '駅・路線で探す'); ?></h2>
        </div>
        <div class="right">
            <ul class="c-btn-list">
                <li><a class="c-btn js-overlay-cancel" href="javascript:void(0)"><?= Yii::t('app', 'キャンセル');?></a></li>
            </ul>
        </div>
    </div>
    <div class="c-page-head-lead-message-block">
        <div class="page-head-lead-message"><?= Yii::t('app', '働きたい駅・路線を選択してください。'); ?></div>
    </div>
    <div class="c-overlay-content-wrap-block">
        <div class="s-overlay-select-category-block js-overlay-content-move prefList">
            <div class="overlay-select-category">
                <?= Html::beginForm('', 'post', ['id' => 'ajaxStationForm']) ?>
                <?= Html::hiddenInput('station_parent_string', implode(',', $stationParents)) ?>
                <?= Html::hiddenInput('station_string', implode(',', $stations)) ?>
                <ul class="s-select-category-list">

                    <?php foreach ($searchForm->prefs as $pref): ?>

                        <?= Html::tag(
                            'li',
                            Html::a(
                                Html::img('/pict/arrow-black.svg', [
                                    'class' => 'list-icon',
                                    'width' => 7,
                                ]) .
                                Html::tag('span', Html::encode($pref->pref_name), [
                                    'class' => 'name',
                                ]),
                                'javascript:void(0)',
                                [
                                    'class'              => [
                                        'list-bar',
                                        'js-overlay-content-trigger-next',
                                        'js-change-submit',
                                    ],
                                    'data-next'          => "train-{$pref->pref_no}",
                                    'data-form-selector' => '#ajaxStationForm',
                                    'data-action'        => Url::to(['/kyujin/ajax-station', 'prefNo' => $pref->pref_no]),
                                ]
                            ),
                            [
                                'class' => 'list',
                            ]
                        ); ?>
                    <?php endforeach; ?>

                </ul>
                <?= Html::endForm() ?>
            </div>
        </div>

        <?php Pjax::begin([
            'enablePushState' => false,
            'formSelector'    => '#ajaxStationForm',
            'options'         => [
                'id'    => 'ajaxStation',
                'class' => 's-overlay-selected-category-block js-overlay-content-move is-hidden prefContent',
            ],
        ]) ?>
        <?php Pjax::end() ?>

        <?= $this->render('_overlay-fixed-footer', [
            'identifier' => $identifier,
            'searchForm' => $searchForm,
        ]); ?>

    </div>
</div>
<!-- /駅用選択フロー ===================================================//-->


