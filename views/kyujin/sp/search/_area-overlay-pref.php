<?php
/**
 * 地域検索オーバーレイ
 * 都道府県ごとのajax表示部分
 */
use app\models\forms\JobSearchForm;
use app\models\manage\searchkey\Pref;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this       View */
/* @var $searchForm JobSearchForm */
/* @var $pref       Pref */

Pjax::begin([
    'enablePushState' => false,
    'options'         => [
        'id'    => 'ajaxArea',
        'class' => 's-overlay-selected-category-block js-overlay-content-move',
    ],
]);

$identifier = 'pref_dist_master'; // オーバーレイ識別子
?>
<div class="overlay-selected-category s-select-area-block loadingAjax">

    <div class="c-title-label op-mb0">
        <div class="title pref" data-pref-no="<?= Html::encode($pref->pref_no); ?>"><?= Html::encode($pref->pref_name) ?></div>
    </div>

    <?php foreach ($pref->dispPrefDistMasters as $prefDist): ?>
        <?php if ($prefDist->valid_chk && $prefDist->districts): ?>
            <div class="c-selected-category-list js-selected-category in-overlay">
                <div class="c-selected-category-title-label">

                    <div class="left">
                        <h2 class="title" data-parent-id="<?= Html::encode($prefDist->pref_dist_master_no); ?>">
                            <?= Html::encode($prefDist->pref_dist_name) ?>
                        </h2>
                    </div>

                    <div class="right">
                        <ul class="c-btn-list">
                            <?= Html::tag(
                                'li',
                                Html::a(
                                    Html::encode(Yii::t('app', '詳細選択')),
                                    'javascript:void(0)',
                                    [
                                        'class' => [
                                            'c-btn',
                                            'op-link',
                                            'js-selected-category-open',
                                        ],
                                    ]
                                ) .
                                Html::a(
                                    Html::encode(Yii::t('app', 'クリア')),
                                    'javascript:void(0)',
                                    [
                                        'class' => [
                                            'c-btn',
                                            'js-selected-category-clear',
                                            'js-op-hierarchy',
                                        ],
                                    ]
                                )
                            ); ?>
                            <?= Html::tag(
                                'li',
                                Html::a(
                                    Html::encode(Yii::t('app', '全選択')),
                                    'javascript:void(0)',
                                    [
                                        'class' => [
                                            'c-btn',
                                            'op-link',
                                            'js-btn-select-all-toggle',
                                        ],
                                    ]
                                )
                            ); ?>
                        </ul>
                    </div>

                </div>
                <div class="select-area js-show-toggle">
                    <div class="s-btn-check-wrap-block">
                        <ul class="c-btn-check-wrap">

                            <?php foreach ($prefDist->districts as $district): ?>
                                <?= Html::tag(
                                    'li',
                                    Html::input(
                                        'checkbox',
                                        'btn',
                                        $district->dist_cd,
                                        [
                                            'checked' => in_array($district->dist_cd, (array)$searchForm->pref_dist_master) ||
                                                in_array($prefDist->pref_dist_master_no, (array)$searchForm->pref_dist_master_parent) ||
                                                in_array($pref->pref_no, (array)$searchForm->pref),
                                        ]
                                    ) .
                                    Html::tag('label', Html::encode($district->dist_name)),
                                    [
                                        'class'       => [
                                            'list',
                                            'c-btn-check',
                                            'in-overlay',
                                        ],
                                        'data-target' => $identifier,
                                    ]
                                ); ?>
                            <?php endforeach; ?>

                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div>
<?php Pjax::end() ?>
