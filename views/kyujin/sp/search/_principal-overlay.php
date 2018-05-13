<?php
/**
 * 優先キー検索オーバーレイ
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchForm JobSearchForm */

$identifier = 'principal'; // オーバーレイ識別子
?>
<!-- 優先キー用選択フロー ===================================================-->
<div class="s-overlay-wrap-block js-overlay" id="ovl-job">
    <div class="c-title-label op-thin">
        <div class="left">
            <h2 class="title"><?= Yii::t('app', '{key}で探す', ['key' => $searchForm->principalKey->searchkey_name]); ?></h2>
        </div>
        <div class="right">
            <ul class="c-btn-list">
                <li><a class="c-btn js-overlay-cancel" href="javascript:void(0)"><?= Yii::t('app', 'キャンセル');?></a></li>
            </ul>
        </div>
    </div>
    <div class="c-page-head-lead-message-block">
        <div class="page-head-lead-message">
            <?= Yii::t('app', '{key}を選択してください', ['key' => $searchForm->principalKey->searchkey_name]); ?>
        </div>
    </div>

    <div class="c-overlay-content-wrap-block">
        <div class="s-overlay-selected-category-block js-overlay-content-move">
            <div class="overlay-selected-category s-select-area-block">

                <?php foreach ($searchForm->principalCategories as $category): ?>

                    <?php if ($category->items): ?>
                        <div class="c-selected-category-list js-selected-category in-overlay">
                            <div class="c-selected-category-title-label">
                                <div class="left">
                                    <h2 class="title" data-parent-id="<?= Html::encode($category->searchkey_category_no); ?>">
                                        <?= Html::encode($category->searchkey_category_name) ?>
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

                                        <?php foreach ($category->items as $item): ?>
                                            <?= Html::tag(
                                                'li',
                                                Html::input(
                                                    'checkbox',
                                                    'btn',
                                                    $item->searchkey_item_no,
                                                    [
                                                        'checked' => in_array($item->searchkey_item_no, (array)$searchForm->{$searchForm->principalKey->table_name}) ||
                                                            in_array($category->searchkey_category_no, (array)$searchForm->{$searchForm->principalKey->table_name . '_parent'}),
                                                    ]
                                                ) .
                                                Html::tag('label', Html::encode($item->searchkey_item_name)),
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
        </div>

        <?= $this->render('_overlay-fixed-footer', [
            'identifier' => $identifier,
            'searchForm' => $searchForm,
        ]); ?>

    </div>
</div>
<!-- /優先キー用選択フロー ===================================================//-->

