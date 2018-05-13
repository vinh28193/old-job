<?php
/**
 * 地域検索オーバーレイ
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchForm JobSearchForm */
/* @var $prefs array */
/* @var $prefDistMasterParents array */
/* @var $prefDistMasters array */

$identifier = 'pref_dist_master'; // オーバーレイ識別子
?>
<!-- 地域用選択フロー ===================================================-->
<div class="s-overlay-wrap-block js-overlay" id="ovl-area">
    <div class="c-title-label op-thin">
        <div class="left">
            <h2 class="title"><?= Yii::t('app', '地域で探す'); ?></h2>
        </div>
        <div class="right">
            <ul class="c-btn-list">
                <li><a class="c-btn js-overlay-cancel" href="javascript:void(0)"><?= Yii::t('app', 'キャンセル');?></a></li>
            </ul>
        </div>
    </div>
    <div class="c-page-head-lead-message-block">
        <div class="page-head-lead-message"><?= Yii::t('app', '働きたい地域を選択してください。'); ?></div>
    </div>

    <div class="c-overlay-content-wrap-block">

        <div class="s-overlay-select-category-block js-overlay-content-move prefList">
            <div class="overlay-select-category">
                <?= Html::beginForm('', 'post', ['id' => 'ajaxAreaForm']) ?>
                <?= Html::hiddenInput('pref_string', implode(',', $prefs)) ?>
                <?= Html::hiddenInput('pref_dist_master_parent_string', implode(',', $prefDistMasterParents)) ?>
                <?= Html::hiddenInput('pref_dist_master_string', implode(',', $prefDistMasters)) ?>
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
                                    'data-next'          => "pref-{$pref->pref_no}",
                                    'data-form-selector' => '#ajaxAreaForm',
                                    'data-action'        => Url::to(['/kyujin/ajax-area', 'prefNo' => $pref->pref_no]),
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
            'formSelector'    => '#ajaxAreaForm',
            'options'         => [
                'id'    => 'ajaxArea',
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
<!-- /地域用選択フロー ===================================================//-->

