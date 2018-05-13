<?php

use app\models\forms\JobSearchForm;
use app\models\manage\SearchkeyMaster;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\bootstrap\BootstrapPluginAsset;

BootstrapPluginAsset::register($this);

/* @var $this View */
/* @var $searchForm JobSearchForm */
/* @var $conditions array */
/* @var $selectArray array  選択内容 */
/* @var $urlString string  現在の検索状態のURL */
/* @var $conditionsString string  $searchFormへロードした配列をシリアライズしたもの */

// スマホ優先キー
$principalKey = $searchForm->principalKey;
// スマホ優先キーの親の名前
$principalChild = $principalKey->table_name ?? '';
// スマホ優先キーの子の名前
$principalParent = $principalChild . '_parent';

$prefJson = Json::encode((array)$searchForm->pref);
$prefDistMasterParentJson = Json::encode((array)$searchForm->pref_dist_master_parent);
$prefDistMasterJson = Json::encode((array)$searchForm->pref_dist_master);
$stationParentJson = Json::encode((array)$searchForm->station_parent);
$stationJson = Json::encode((array)$searchForm->station);

// 戻るや進むの場合は強制リロード
$forceReloadJs = <<<JS
if ($('#cacheFlg').val() === 'new') {
    $('#cacheFlg').val('old');
} else {
    location.reload();
}
JS;
$this->registerJs($forceReloadJs, View::POS_END);

$nameJsonJs = <<<JS
var prefs = {$prefJson};
var prefDistMasterParents = {$prefDistMasterParentJson};
var prefDistMasters = {$prefDistMasterJson};
var stationParents = {$stationParentJson};
var stations = {$stationJson};

function appendListItem(target, itemId, text) {
    var targetId = '.' + target;
    var value = itemId.replace(target + '-', '');
    // フッタ選択中
    if (!$('.selecting-list-item .list-item').find('.' + itemId).length) {
        $('.selecting-list-item .list-item').append(
            '<span class="' + itemId + '">' + text + '</span>'
        );
    }
    // フォーム上部
    var itemBlock = $('.s-seleced-item-block' + targetId);
    if (itemBlock.length) {
        itemBlock.show();
        $('.c-btn-check-wrap' + targetId).append(
            '<li class="selected-item list c-btn-check is-selected ' + itemId + '" data-target="' + target + '" data-number="' + value + '">' +
            '<input value="" name="btn" type="checkbox"/>' +
            '<label>' + text + '</label>' +
            '</li>'
        );
    }
}

$.each(prefs, function(text, value) {
    var target= 'pref';
    appendListItem(target, target + '-' + value, text);
});
$.each(prefDistMasterParents, function(text, value) {
    var target= 'pref_dist_master';
    appendListItem(target, target + '-parent-' + value, text);
});
$.each(prefDistMasters, function(text, value) {
    var target= 'pref_dist_master';
    appendListItem(target, target + '-' + value, text);
});
$.each(stationParents, function(text, value) {
    var target= 'station_parent';
    appendListItem(target, target + '-' + value, text);
});
$.each(stations, function(text, value) {
    var target= 'station';
    appendListItem(target, target + '-' + value, text);
})
JS;
$this->registerJs($nameJsonJs);
?>

<input id="cacheFlg" type="hidden" value="new">

<?= $this->render('search/_area-overlay', [
    'searchForm' => $searchForm,
    'prefs' => $searchForm->pref ?? [],
    'prefDistMasterParents' => $searchForm->pref_dist_master_parent ?? [],
    'prefDistMasters' => $searchForm->pref_dist_master ?? [],
]); ?>
<?php if ($searchForm->hasStationKey()): ?>
    <?= $this->render('search/_station-overlay', [
        'searchForm' => $searchForm,
        'stationParents' => $searchForm->station_parent ?? [],
        'stations' => $searchForm->station ?? [],
    ]); ?>
<?php endif; ?>
<?php if ($searchForm->principalKey): ?>
    <?= $this->render('search/_principal-overlay', [
        'searchForm' => $searchForm,
    ]); ?>
<?php endif; ?>

<div class="container subcontainer flexcontainer">
    <div class="row">
        <!-- ▼ここからコンテンツスタート▼-->
        <div class="col-sm-12">
            <h1 class="resultTitle"><em><?= Yii::t('app', '詳細検索'); ?></em></h1>
            <div class="c-title-page-header-block">
                <h2 class="title-page-header"><?= Yii::t('app', '詳細条件から仕事を探す'); ?></h2>
            </div>

            <?= Html::beginForm('search-result', 'post', [
                'id' => 'search-form',
            ]); ?>
            <?= Html::hiddenInput('area', $searchForm->area) ?>

            <?= Html::hiddenInput('pref_string', implode(',', (array)$searchForm->pref ?: []), ['id' => 'hidden-pref']); ?>
            <?= Html::hiddenInput('pref_dist_master_parent_string', implode(',', (array)$searchForm->pref_dist_master_parent ?: []),
                ['id' => 'hidden-pref_dist_master_parent']); ?>
            <?= Html::hiddenInput('pref_dist_master_string', implode(',', (array)$searchForm->pref_dist_master ?: []),
                ['id' => 'hidden-pref_dist_master']); ?>

            <?= Html::hiddenInput('station_parent_string', implode(',', (array)$searchForm->station_parent ?: []),
                ['id' => 'hidden-station_parent']); ?>
            <?= Html::hiddenInput('station_string', implode(',', (array)$searchForm->station ?: []), ['id' => 'hidden-station']); ?>

            <?php if ($searchForm->principalKey): ?>
                <?= Html::hiddenInput('principal_parent_string', implode(',', (array)$searchForm->{$principalParent} ?: []),
                    ['id' => 'hidden-principal_parent']); ?>
                <?= Html::hiddenInput('principal_string', implode(',', (array)$searchForm->{$principalChild} ?: []),
                    ['id' => 'hidden-principal']); ?>
            <?php endif; ?>

            <!-- 地域/駅・路線 ブロック-->
            <div class="s-select-area-block js-selected-category">
                <div class="c-title-label">
                    <div class="left">
                        <h2 class="title">
                            <?php if ($searchForm->hasStationKey()): ?>
                                <?= $searchForm->searchKeys['pref']->searchkey_name . ' / ' . $searchForm->searchKeys['station']->searchkey_name; ?>
                            <?php else: ?>
                                <?= $searchForm->searchKeys['pref']->searchkey_name; ?>
                            <?php endif; ?>
                        </h2>
                    </div>
                    <div class="right">
                        <ul class="c-btn-list">
                            <li>
                                <a class="c-btn js-selected-category-clear op-active to-overlay place"
                                   href="javascript:void(0)">
                                    <?= Yii::t('app', 'クリア'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--▼選択済みここから▼-->
                <div class="s-seleced-item-block pref pref_dist_master pref_dist_master_parent">
                    <div class="seleced-item-title-block">
                        <div class="seleced-item-title">
                            <?= Yii::t('app', '選択:{area}', ['area' => $searchForm->searchKeys['pref']->searchkey_name]); ?>
                        </div>
                    </div>
                    <div class="seleced-item-item-block">
                        <div class="seleced-item-item">
                            <div class="s-btn-check-wrap-block">
                                <ul class="c-btn-check-wrap pref pref_dist_master pref_dist_master_parent">

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="s-seleced-item-block station station_parent">
                    <div class="seleced-item-title-block">
                        <div class="seleced-item-title">
                            <?= Yii::t('app', '選択:{station}', ['station' => $searchForm->searchKeys['station']->searchkey_name ?? '']); ?>
                        </div>
                    </div>
                    <div class="seleced-item-item-block">
                        <div class="seleced-item-item">
                            <div class="s-btn-check-wrap-block">
                                <ul class="c-btn-check-wrap station station_parent">

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--▲選択済みここまで▲-->
                <div class="select-area">
                    <div class="select-area-text">
                        <?php if ($searchForm->hasStationKey()): ?>
                            <?= Yii::t('app', '{area}か{station}のいずれかを選択してください。', [
                                'area' => $searchForm->searchKeys['pref']->searchkey_name,
                                'station' => $searchForm->searchKeys['station']->searchkey_name,
                            ]); ?>
                        <?php else: ?>
                            <?= Yii::t('app', '{area}を選択してください。', ['area' => $searchForm->searchKeys['pref']->searchkey_name]); ?>
                        <?php endif; ?>
                    </div>
                    <div class="s-btn-wrap-block">
                        <ul class="row">
                            <li class="col-xs-6">
                                <?= Html::a(
                                    Yii::t('app', '{area}を選ぶ',
                                        ['area' => $searchForm->searchKeys['pref']->searchkey_name]) . '<i class="fa fa-chevron-right"></i>',
                                    '#ovl-area',
                                    [
                                        'class' => [
                                            'c-btn-radius',
                                            'op-bg-link',
                                            'op-fz-l',
                                            'js-overlay-trigger-on',
                                        ],
                                    ]
                                ) ?>
                            </li>
                            <?php if ($searchForm->hasStationKey()): ?>
                                <li class="col-xs-6">
                                    <?= Html::a(
                                        Yii::t('app', '{station}を選ぶ',
                                            ['station' => $searchForm->searchKeys['station']->searchkey_name] ?? '') . '<i class="fa fa-chevron-right"></i>',
                                        '#ovl-train',
                                        [
                                            'class' => [
                                                'c-btn-radius',
                                                'op-bg-link',
                                                'op-fz-l',
                                                'js-overlay-trigger-on',
                                            ],
                                        ]
                                    ) ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- .s-select-area-block-->
            <!-- 優先キー ブロック-->
            <?php if ($searchForm->principalKey): ?>
                <div class="s-select-area-block js-selected-category">
                    <div class="c-title-label">
                        <div class="left">
                            <h2 class="title"><?= $principalKey->searchkey_name; ?></h2>
                        </div>
                        <div class="right">
                            <ul class="c-btn-list">
                                <li>
                                    <a class="c-btn js-selected-category-clear op-active to-overlay principal"
                                       href="javascript:void(0)">
                                        <?= Yii::t('app', 'クリア'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!--▼選択済みここから▼-->
                    <div class="s-seleced-item-block principal principal_parent">
                        <div class="seleced-item-item-block">
                            <div class="seleced-item-item">
                                <div class="s-btn-check-wrap-block">
                                    <ul class="c-btn-check-wrap principal principal_parent">

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--▲選択済みここまで▲-->
                    <div class="select-area">
                        <div class="s-btn-wrap-block">
                            <ul class="row">
                                <li class="col-xs-12">
                                    <?= Html::a(
                                        Yii::t('app', '{key}を選ぶ', [
                                            'key' => $principalKey->searchkey_name,
                                        ]) . '<i class="fa fa-chevron-right"></i>',
                                        '#ovl-job',
                                        [
                                            'class' => [
                                                'c-btn-radius',
                                                'op-bg-link',
                                                'op-fz-l',
                                                'js-overlay-trigger-on',
                                            ],
                                        ]
                                    ) ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($searchForm->searchKeys as $searchKey): ?>
                <?php if ($searchKey->isWage && $searchForm->wages): ?>
                    <!-- 給与 ブロック-->
                    <?= Html::hiddenInput('wage_category_parent_string', implode(',', (array)$searchForm->wage_category_parent), [
                        'id' => 'hidden-wage-category',
                    ]); ?>

                    <div class="s-select-area-block js-selected-category js-wage-wrap-block">
                        <div class="c-title-label">
                            <div class="left">
                                <h2 class="title"><?= $searchForm->searchKeys['wage_category']->searchkey_name ?></h2>
                            </div>
                            <div class="right">
                                <ul class="c-btn-list">
                                    <li>
                                        <a class="c-btn js-selected-category-clear op-active wage"
                                           href="javascript:void(0)">
                                            <?= Yii::t('app', 'クリア'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="select-area js-tab-select-wrap">
                            <div class="s-btn-wrap-block">
                                <ul class="row op-reset">
                                    <?php foreach ($searchForm->wages as $wageCategoryNo => $wageCategory): ?>
                                        <?php if ($wageCategory->wageItemValid): ?>
                                            <li class="col-xs-3">
                                                <a class="c-btn-radius op-link js-tab-select-btn"
                                                   href="#money-tab<?= $wageCategoryNo ?>"
                                                   data-target="wage-category" data-value="<?= $wageCategoryNo ?>">
                                                    <?= Html::encode(Yii::t('app', $wageCategory->wage_category_name)); ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <!-- 給与タブ ===================================================-->
                            <div class="s-select-area-tab-wrap-block">
                                <div class="select-area-tab-wrap">

                                    <?php foreach ($searchForm->wages as $wageCategoryNo => $wageCategory): ?>
                                        <?php if ($wageCategory->wageItemValid): ?>
                                            <div class="js-tab-content" id="money-tab<?= $wageCategoryNo ?>">
                                                <div class="s-btn-radio-wrap-block">
                                                    <ul class="c-btn-check-wrap">

                                                        <?php foreach ($wageCategory->wageItemValid as $j => $item): ?>
                                                            <?= Html::tag(
                                                                'li',
                                                                Html::input(
                                                                    'radio',
                                                                    'wage_category',
                                                                    $item->wage_item_no,
                                                                    [
                                                                        'id' => $wageCategoryNo . '-' . $j,
                                                                        'checked' => in_array($item->wage_item_no,
                                                                            (array)$searchForm->wage_category),
                                                                    ]
                                                                ) .
                                                                Html::tag(
                                                                    'label',
                                                                    Html::encode($item->disp_price),
                                                                    [
                                                                        'for' => $wageCategoryNo . '-' . $j,
                                                                    ]
                                                                ),
                                                                [
                                                                    'class' => [
                                                                        'list',
                                                                        'c-btn-radio',
                                                                        'wage',
                                                                    ],
                                                                    'data-target' => 'wage',
                                                                ]
                                                            ); ?>
                                                        <?php endforeach; ?>

                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                            <!-- /給与タブ ===================================================//-->
                        </div>
                    </div>
                    <!-- .s-select-area-block-->
                <?php elseif (
                    $searchKey->isCategory
                    && ($searchKey->table_name != ($searchForm->principalKey->table_name ?? ''))
                    && $searchKey->categories
                ): ?>
                    <!-- 2階層 ブロック-->
                    <?php
                    $parentAttribute = "searchkey_category{$searchKey->categoryId}_parent";
                    $childAttribute = "searchkey_category{$searchKey->categoryId}";
                    $parentFormName = "{$parentAttribute}_string";
                    $childFormName = "{$childAttribute}_string";
                    $isDropDown = $searchKey->search_input_tool == SearchkeyMaster::SEARCH_INPUT_TOOL_DROPDOWN;

                    // カテゴリーがカンマ区切りで入るhidden
                    echo Html::hiddenInput($parentFormName, implode(',', (array)$searchForm->$parentAttribute), ['id' => "hidden-{$parentAttribute}"]);
                    // 小項目がカンマ区切りで入るhidden
                    echo Html::hiddenInput($childFormName, implode(',', (array)$searchForm->$childAttribute), ['id' => "hidden-{$childAttribute}"]);
                    ?>
                    <div class="s-select-area-block js-selected-category c-selected-category-list">
                        <div class="c-title-label">
                            <div class="left">
                                <h2 class="title"><?= Yii::t('app', Html::encode($searchKey->searchkey_name)); ?></h2>
                            </div>
                            <div class="right">
                                <ul class="c-btn-list">
                                    <li>
                                        <a class="c-btn js-selected-category-clear op-active" href="javascript:void(0)">
                                            <?= Yii::t('app', 'クリア'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php if ($isDropDown): ?>
                            <div class="select-area js-tab-select-wrap js-dropdown-wrap-block">
                                <div class="s-btn-wrap-block">
                                    <ul class="row op-reset op-one-column">
                                        <?php foreach ($searchKey->categories as $category): ?>
                                            <?php if ($category->items): ?>
                                                <?php $dropdown_id = join('-', ['dropdown', $searchKey->table_name, $category->id]); ?>
                                                <li>
                                                    <?= Html::a(Yii::t('app', Html::encode($category->searchkey_category_name)), '#' . $dropdown_id, [
                                                        'class' => 'c-btn-radius op-link js-tab-select-btn'
                                                            . (in_array($category->searchkey_category_no, (array)$searchForm->$parentAttribute) ?
                                                                ' is-selected' : ''),
                                                        'data-hidden-id' => '#hidden-' . $parentAttribute,
                                                        'data-value' => $category->searchkey_category_no,

                                                    ]); ?>
                                                    <div class="s-select-area-tab-wrap-block">
                                                        <div class="select-area-tab-wrap">
                                                            <?= Html::beginTag('div', [
                                                                'id' => $dropdown_id,
                                                                'class' => 'js-tab-content',
                                                                'style' => in_array($category->searchkey_category_no, (array)$searchForm->$parentAttribute) ?
                                                                    'display: block;' : '',
                                                            ]); ?>
                                                            <div class="s-btn-radio-wrap-block">
                                                                <ul class="c-btn-check-wrap">
                                                                    <?php foreach ($category->items as $j => $item):
                                                                        $item_id = join('-', ['dropdown', 'item', $searchKey->table_name, $category->id, $item->id]);
                                                                        ?>
                                                                        <?= Html::tag(
                                                                        'li',
                                                                        Html::input(
                                                                            'radio',
                                                                            $childAttribute . '_string',
                                                                            $item->searchkey_item_no,
                                                                            [
                                                                                'id' => $item_id,
                                                                                'checked' => in_array($item->searchkey_item_no,
                                                                                    (array)$searchForm->$childAttribute),
                                                                            ]
                                                                        ) .
                                                                        Html::tag(
                                                                            'label',
                                                                            Html::encode($item->searchkey_item_name),
                                                                            [
                                                                                'for' => $item_id,
                                                                            ]
                                                                        ),
                                                                        [
                                                                            'class' => [
                                                                                'list',
                                                                                'c-btn-radio',
                                                                                'two-level',
                                                                            ],
                                                                            'data-target' => $childAttribute,
                                                                        ]
                                                                    ); ?>
                                                                    <?php endforeach; ?>
                                                                </ul>
                                                            </div>
                                                            <?= Html::endTag('div') ?>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="select-area">
                                <div class="s-btn-accordion-block">

                                    <?php foreach ($searchKey->categories as $category): ?>
                                        <?php if ($category->items): ?>
                                            <div class="s-btn-accordion-box js-accordion-box-btn">
                                                <div class="c-btn-accordion">
                                                    <div class="left">
                                                        <div class="title"
                                                             data-parent-id="<?= Html::encode($category->searchkey_category_no); ?>">
                                                            <?= Yii::t('app', Html::encode($category->searchkey_category_name)); ?>
                                                        </div>
                                                    </div>
                                                    <div class="right">
                                                        <div class="s-btn-accordion-change-block">
                                                            <img class="acd-arrow" src="/pict/arrow-blue.svg" alt="">
                                                            <a class="c-btn js-selected-category-clear js-op-acd"
                                                               href="javascript:void(0)">
                                                                <?= Yii::t('app', 'クリア'); ?>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="s-btn-check-wrap-block js-selected-category">
                                                    <ul class="c-btn-check-wrap">

                                                        <?php foreach ($category->items as $j => $item): ?>

                                                            <?= Html::tag(
                                                                'li',
                                                                Html::input(
                                                                    'checkbox',
                                                                    'category',
                                                                    $item->searchkey_item_no,
                                                                    [
                                                                        'id' => $childAttribute . '-' . $j,
                                                                        'checked' => in_array($item->searchkey_item_no,
                                                                                (array)$searchForm->$childAttribute) ||
                                                                            in_array($category->searchkey_category_no,
                                                                                (array)$searchForm->$parentAttribute),
                                                                    ]
                                                                ) .
                                                                Html::tag(
                                                                    'label',
                                                                    Html::encode($item->searchkey_item_name),
                                                                    [
                                                                        'for' => $childAttribute . '-' . $j,
                                                                    ]
                                                                ),
                                                                [
                                                                    'class' => [
                                                                        'list',
                                                                        'c-btn-check',
                                                                        'two-level',
                                                                    ],
                                                                    'data-target' => $childAttribute,
                                                                ]
                                                            ); ?>

                                                        <?php endforeach; ?>

                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <!-- .s-select-area-block-->
                <?php elseif ($searchKey->isItem && $searchKey->items): ?>
                    <!-- 1階層 ブロック-->
                    <?php
                    $attribute = "searchkey_item{$searchKey->itemId}";
                    $formName = "{$attribute}_string";
                    $isDropDown = $searchKey->search_input_tool == SearchkeyMaster::SEARCH_INPUT_TOOL_DROPDOWN;

                    // 小項目がカンマ区切りで入るhidden
                    echo Html::hiddenInput($formName, implode(',', (array)$searchForm->$attribute), ['id' => "hidden-{$attribute}"]);
                    ?>

                    <div class="s-select-area-block js-selected-category">
                        <div class="c-title-label">
                            <div class="left">
                                <h2 class="title"><?= Yii::t('app', $searchKey->searchkey_name); ?></h2>
                            </div>
                            <div class="right">
                                <ul class="c-btn-list">
                                    <li>
                                        <a class="c-btn js-selected-category-clear op-active" href="javascript:void(0)">
                                            <?= Yii::t('app', 'クリア'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="select-area <?= $isDropDown ? 'js-dropdown-wrap-block' : '' ?>">
                            <div class="s-btn-check-wrap-block c-selected-category-list">
                                <ul class="c-btn-check-wrap">
                                    <?php foreach ($searchKey->items as $j => $item): ?>
                                        <?= Html::tag(
                                            'li',
                                            Html::input(
                                                $isDropDown ? 'radio' : 'checkbox',
                                                $isDropDown ? ($attribute . '_string') : 'item',
                                                $item->searchkey_item_no,
                                                [
                                                    'id' => $searchKey->searchkey_no . '-' . $j,
                                                    'checked' => in_array($item->searchkey_item_no, (array)$searchForm->$attribute),
                                                ]
                                            ) .
                                            Html::tag(
                                                'label',
                                                Html::encode($item->searchkey_item_name)
                                            ),
                                            [
                                                'class' => [
                                                    'list',
                                                    $isDropDown ? 'c-btn-radio' : 'c-btn-check',
                                                ],
                                                'data-target' => $attribute,
                                            ]
                                        ); ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- .s-select-area-block-->
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="s-fix-footer-btn-block">
                <div class="fix-footer-btn">
                    <div class="s-selecting-list-block">
                        <div class="selecting-list">
                            <div class="selecting-list-title">
                                <?= Yii::t('app', '選択中の項目'); ?>
                            </div>
                            <div class="selecting-list-item">
                                <div class="list-item"></div>
                            </div>
                        </div>
                    </div>
                    <div class="c-btn-push js-overlay-trigger-off">
                        <?= Html::a(
                            Html::encode(Yii::t('app', '検索')) . Html::tag('span', '（0）', ['class' => 'searchCount parentCount']),
                            'javascript:void(0)',
                            [
                                'id' => 'search-submit',
                            ]
                        ) ?>
                    </div>
                </div>
            </div>

            <?= Html::endForm(); ?>

        </div>
        <!-- ▲ここまでコンテンツ▲-->
    </div>
</div>
