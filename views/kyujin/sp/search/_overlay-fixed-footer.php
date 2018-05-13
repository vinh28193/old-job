<?php
/**
 * オーバーレイのフッタ
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $identifier string */
/* @var $searchForm JobSearchForm */

?>
<div class="s-fix-footer-btn-block">
    <div class="fix-footer-btn<?= ($searchForm->scenario == JobSearchForm::SCENARIO_RESULT) ? '' : ' op-fixed'?>">
        <div class="s-selecting-list-block">
            <div class="selecting-list">
                <div class="selecting-list-title"><?= Yii::t('app', '選択中の項目'); ?></div>
                <div class="selecting-list-item">
                    <div class="list-item <?= $identifier; ?>"></div>
                </div>
            </div>
        </div>
        <div class="c-btn-push<?= ($searchForm->scenario == JobSearchForm::SCENARIO_RESULT) ? '' : ' js-overlay-trigger-off'?>">
            <?php if ($searchForm->scenario == JobSearchForm::SCENARIO_AREA_TOP): ?>
                <?= Html::a(
                    Html::encode(Yii::t('app', '確定')),
                    'javascript:void(0)',
                    ['class' => 'top-overlay-fixed']
                ) ?>
            <?php elseif ($searchForm->scenario == JobSearchForm::SCENARIO_RESULT): ?>
                <?= Html::a(
                    Html::encode(Yii::t('app', '検索')) . Html::tag('span', '(0)', ['class' => 'searchCount']),
                    'javascript:void(0)',
                    ['class' => 'js-change-submit', 'data' => ['form-selector' => '#searchForm', 'action' => '/kyujin/search-result']]
                ) ?>
            <?php elseif ($searchForm->scenario == JobSearchForm::SCENARIO_DETAIL): ?>
                <?= Html::a(
                    Html::encode(Yii::t('app', '確定')) . Html::tag('span', '(0)', ['class' => 'searchCount']),
                    'javascript:void(0)'
                ) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
