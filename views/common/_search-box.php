<?php
/**
 * 詳細検索 View
 */
use app\assets\MainAsset;
use app\models\forms\JobSearchForm;
use app\models\manage\SearchkeyMaster;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchForm JobSearchForm */
/* @var $allCount integer */

$this->registerJsFile('/js/module.js', [
    'depends' => MainAsset::className(),
]);

// スマホ優先キー
$principalKey = $searchForm->principalKey;
// スマホ優先キーの親の名前
$principalChild = $principalKey->table_name ?? '';
// スマホ優先キーの子の名前
$principalParent = $principalChild . '_parent';

$this->params['bodyClass'] = 'type-pc';

$array = [];
foreach ($searchForm->wages as $wageCategory) {
    foreach ($wageCategory->wageItem as $wageItem) {
        $array[$wageCategory->wage_category_no][$wageItem->wage_item_no] = $wageItem->disp_price;
    }
}
$wageJson = \yii\helpers\Json::encode($array);

$wageJs = <<<JS
var wageItems = {$wageJson};
function wageItem(select) {
  $('.wage-category').remove();
  var categoryNo = $(select).val();
  $.each(wageItems[categoryNo], function(val, text) {
    $('#s-wage-item').append($('<option>').val(val).text(text).addClass('wage-category'));
  });
}
$('#s-wage-category').on('change', function () {
    wageItem(this);
});
JS;

$this->registerJs($wageJs);

?>
<?= Html::hiddenInput('area', $searchForm->area) ?>

<div id="hidden-group-pref_dist_master">
    <?php
    // 地域選択 hidden
    echo Html::hiddenInput($name = 'pref_string', Yii::$app->request->post($name), [
        'id' => 'hidden-pref',
    ]);
    echo Html::hiddenInput($name = 'pref_dist_master_parent_string', Yii::$app->request->post($name), [
        'id' => 'hidden-pref_dist_master_parent',
    ]);
    echo Html::hiddenInput($name = 'pref_dist_master_string', Yii::$app->request->post($name), [
        'id' => 'hidden-pref_dist_master',
    ]);
    ?>
</div>

<div id="hidden-group-station">
    <?php
    // 駅選択 hidden
    echo Html::hiddenInput($name = 'station_parent_string', Yii::$app->request->post($name), [
        'id' => 'hidden-station_parent',
    ]);
    echo Html::hiddenInput($name = 'station_string', Yii::$app->request->post($name), [
        'id' => 'hidden-station',
    ]);
    ?>
</div>


<?php
// 優先検索 hidden
echo Html::hiddenInput($name = 'principal_parent_string', Yii::$app->request->post($name), [
    'id' => 'hidden-principal_parent',
]);
echo Html::hiddenInput($name = 'principal_string', Yii::$app->request->post($name), [
    'id' => 'hidden-principal',
]);
?>
<div class="searchBox">
    <table>
        <tr>
            <th><?= Yii::t('app', 'キーワード'); ?></th>
            <td>
                <div class="searchField op-search-fit">
                    <?= Html::input(
                        'text',
                        'keyword',
                        $searchForm->keyword,
                        [
                            'class' => 'form-control',
                            'placeholder' => Yii::t('app', 'キーワードを入力')
                        ]
                    ); ?>
                </div>
            </td>
        </tr>

        <?php if ($searchForm->hasStationKey() || $searchForm->hasPrefKey()): ?>
            <tr>
                <th><?= Yii::t('app', '勤務地'); ?></th>
                <td>
                    <div class="s-select-overlay-only-block js-select-overlay-only">
                        <div class="select-overlay-only">
                            <?php if ($searchForm->hasStationKey()): ?>
                                <div class="select-overlay-only-list op-float">
                                    <?= Html::a(
                                        Html::tag('span', '', [
                                            'class' => [
                                                'fa',
                                                'fa-plus',
                                            ],
                                        ]) . ' ' . Yii::t('app', '駅を選択する'),
                                        '#search-modal-railway',
                                        [
                                            'id' => 'modal-station-btn',
                                            'class' => [
                                                'mod-btn9',
                                                'c-select-only',
                                                'js-modal-open',
                                            ],
                                            'data-toggle' => 'modal',
                                        ]
                                    ) ?>
                                    <div class="check-field clearfix">
                                        <div class="station">
                                            <!-- 選択中の項目-->
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($searchForm->hasPrefKey()): ?>
                                <div class="select-overlay-only-list op-float">
                                    <?= Html::a(
                                        Html::tag('span', '', [
                                            'class' => [
                                                'fa',
                                                'fa-plus',
                                            ],
                                        ]) . ' ' . Yii::t('app', '地域を選択する'),
                                        '#search-modal-area',
                                        [
                                            'id' => 'modal-pref_dist_master-btn',
                                            'class' => [
                                                'mod-btn9',
                                                'c-select-only',
                                                'js-modal-open',
                                            ],
                                            'data-toggle' => 'modal',
                                        ]
                                    ) ?>
                                    <div class="check-field clearfix">
                                        <div class="pref_dist_master">
                                            <!-- 選択中の項目-->
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($searchForm->principalKey): ?>
            <tr>
                <th><?= Html::encode($searchForm->principalKey->searchkey_name); ?></th>
                <td>
                    <div class="s-select-overlay-only-block js-select-overlay-only">
                        <div class="select-overlay-only">
                            <div class="select-overlay-only-list op-float">
                                <?= Html::a(
                                    Html::tag('span', '', [
                                        'class' => [
                                            'fa',
                                            'fa-plus',
                                        ],
                                    ]) . Yii::t('app', '選択する'),
                                    '#search-modal-job',
                                    [
                                        'class' => [
                                            'mod-btn9',
                                            'js-modal-open',
                                        ],
                                        'data-toggle' => 'modal',
                                    ]
                                ) ?>
                                <div class="check-field clearfix">
                                    <div class="principal">
                                        <!-- 選択中の項目-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endif; ?>

        <?php foreach ($searchForm->searchKeys as $searchKey): ?>
            <?php if ($searchKey->isWage && $searchForm->wages): ?>
                <tr>
                    <th><?= $searchKey->searchkey_name; ?></th>
                    <td>
                        <div class="s-form-group-list">
                            <div class="c-form-group-label"><?= Yii::t('app', '給与体系'); ?></div>
                            <div class="form-group">
                                <select name="wage_category_parent" class="form-control" id="s-wage-category">
                                    <option value=""> <?= Yii::t('app', '選択する'); ?></option>
                                    <?php foreach ($searchForm->wages as $i => $wageCategory): ?>
                                        <?php if ($wageCategory->wageItem): ?>
                                            <?php
                                            $selected = false;
                                            if (in_array($wageCategory->wage_category_no, (array)$searchForm->wage_category_parent)) {
                                                $selected = true;
                                            }
                                            foreach (ArrayHelper::getColumn($wageCategory->wageItem, 'wage_item_no') as $no) {
                                                if (in_array($no, (array)$searchForm->wage_category)) {
                                                    $selected = true;
                                                }
                                            }
                                            ?>
                                            <?= Html::tag(
                                                'option',
                                                Html::encode($wageCategory->wage_category_name),
                                                [
                                                    'name' => 'wage_all[]',
                                                    'value' => $wageCategory->wage_category_no,
                                                    'selected' => $selected,
                                                ]
                                            ); ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="s-form-group-list">
                            <div class="c-form-group-label"><?= Yii::t('app', '金額'); ?></div>
                            <div class="form-group">
                                <select class="form-control" name="wage_category" id="s-wage-item">
                                    <option value=""> <?= Yii::t('app', '選択する'); ?></option>
                                    <?php
                                    $selectedCategory = null;
                                    $selectedItemNo = null;
                                    if (Yii::$app->request->post('wage_category_parent')) {
                                        $selectedCategory = $searchForm->wages[Yii::$app->request->post('wage_category_parent')];
                                    }
                                    if (Yii::$app->request->post('wage_category')) {
                                        $selectedItemNo = Yii::$app->request->post('wage_category');
                                        if (!$selectedCategory) {
                                            foreach ($searchForm->wages as $wageCategory) {
                                                if (in_array($selectedItemNo, ArrayHelper::getColumn($wageCategory->wageItem, 'wage_item_no'))) {
                                                    $selectedCategory = $wageCategory;
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    foreach ($selectedCategory->wageItem ?? [] as $item) {
                                        echo Html::tag('option', Html::encode($item->disp_price), [
                                            'name' => $searchKey->table_name . '[]',
                                            'value' => $item->wage_item_no,
                                            'class' => 'wage-category',
                                            'selected' => ($item->wage_item_no == $selectedItemNo),
                                        ]);
                                    }
                                    ?>
                                </select>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </td>
                </tr>

            <?php elseif ($searchKey->isCategory && $searchKey->table_name != $principalChild && $searchKey->categories): ?>
                <?php
                $parentAttribute = "searchkey_category{$searchKey->categoryId}_parent";
                $childAttribute = "searchkey_category{$searchKey->categoryId}";
                $parentFormName = "{$parentAttribute}_string";
                $childFormName = "{$childAttribute}_string";
                ?>
                <tr>
                    <th><?= Html::encode($searchKey->searchkey_name); ?></th>
                    <td>
                        <div class="s-select-overlay-only-block js-select-overlay-only">
                            <div class="select-overlay-only">
                                <div class="select-overlay-only-list op-float">
                                    <?php if ($searchKey->search_input_tool == SearchkeyMaster::SEARCH_INPUT_TOOL_MODAL): ?>
                                        <?php
                                        // カテゴリーがカンマ区切りで入るhidden
                                        echo Html::hiddenInput($parentFormName, implode(
                                            ',',
                                            (array)$searchForm->{$parentAttribute}
                                        ), [
                                            'id' => "hidden-{$parentAttribute}",
                                        ]);
                                        echo Html::hiddenInput($childFormName, Yii::$app->request->post($childFormName) ?: implode(
                                            ',',
                                            (array)Yii::$app->request->post($childAttribute)
                                        ), [
                                            'id' => "hidden-{$childAttribute}",
                                        ]);

                                        echo $this->render('@app/views/kyujin/search-form/_category-modal', [
                                            'searchForm' => $searchForm,
                                            'allCount' => $allCount ?? null,
                                            'searchKey' => $searchKey,
                                            'identifier' => 'category-' . $searchKey->table_name,
                                            'hierarchyFirst' => $parentAttribute,
                                            'hierarchySecond' => $childAttribute,
                                        ]);
                                        ?>
                                        <?= Html::a(
                                            Html::tag(
                                                'span',
                                                '',
                                                [
                                                    'class' => [
                                                        'fa',
                                                        'fa-plus',
                                                    ],
                                                ]
                                            ) . '選択する',
                                            '#search-modal-' . $searchKey->table_name,
                                            [
                                                'class' => 'mod-btn9 js-modal-open',
                                                'data-toggle' => 'modal',
                                            ]
                                        ); ?>

                                    <?php elseif ($searchKey->search_input_tool == SearchkeyMaster::SEARCH_INPUT_TOOL_CHECKBOX): ?>
                                        <!--大項目-->
                                        <?php foreach ($searchKey->categories as $k => $category): ?>
                                            <?php if ($category->items): ?>
                                                <div class="check-field clearfix">
                                                    <div class="mod-checkItem-title">
                                                        <div class="checkItem">
                                                            <?php if ($searchKey->is_category_label == 0): ?>
                                                                <?= Html::input(
                                                                    'checkbox',
                                                                    $searchKey->table_name . '_parent[]',
                                                                    $category->searchkey_category_no,
                                                                    [
                                                                        'id' => 'search_cate_' . $category->tableName() . '_' . $category->searchkey_category_no,
                                                                        'data-toggle' => 'switch',
                                                                        'data-target' => '.searchCate_' . $category->tableName() . '-' . $category->searchkey_category_no,
                                                                        'checked' => in_array(
                                                                            $category->searchkey_category_no,
                                                                            (array)$searchForm->{$parentAttribute}
                                                                        ),
                                                                    ]
                                                                ); ?>
                                                            <?php endif; ?>
                                                            <?= Html::tag(
                                                                'label',
                                                                Html::encode($category->searchkey_category_name),
                                                                [
                                                                    'for' => 'search_cate_' . $category->tableName() . '_' . $category->searchkey_category_no,
                                                                ]
                                                            ); ?>
                                                        </div>
                                                    </div>

                                                    <ul id="item-<?= Html::encode($searchKey->table_name); ?>-<?= $k; ?>-group"
                                                        class="clearfix searchCate_<?= $category->tableName() ?>-<?= $category->searchkey_category_no ?>">
                                                        <?php foreach ($category->items as $i => $item): ?>
                                                            <li>
                                                                <?= Html::input(
                                                                    'checkbox',
                                                                    $searchKey->table_name . '[]',
                                                                    $item->searchkey_item_no,
                                                                    [
                                                                        'id' => "{$searchKey->table_name}-{$category->id}-{$i}",
                                                                        'checked' => in_array(
                                                                            $item->searchkey_item_no,
                                                                            (array)$searchForm->{$childAttribute}
                                                                        ),
                                                                    ]
                                                                ); ?>
                                                                <?= Html::tag(
                                                                    'label',
                                                                    $item->searchkey_item_name,
                                                                    [
                                                                        'class' => 'pref',
                                                                        'for' => "{$searchKey->table_name}-{$category->id}-{$i}",
                                                                    ]
                                                                ) ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                    <?php else: ?>

                                        <div class="s-form-group-list">
                                            <div class="form-group">
                                                <?php if ($searchKey->is_category_label == 0) {
                                                    $getCategoryItemsList = $searchForm->getCategoryItemsList($searchKey->categories);
                                                    if (is_array(Yii::$app->request->post($searchKey->table_name . '_parent'))) {
                                                        // 検索結果からの戻り（カテゴリ検索）
                                                        $selectedValue = 'cate_' . current(Yii::$app->request->post($searchKey->table_name . '_parent'));
                                                        $getCategoryItemsList['list'] = array_merge($getCategoryItemsList['list'], [$selectedValue => ['selected' => true, 'style' => 'font-weight: bold;']]);
                                                    } elseif (is_array(Yii::$app->request->post($searchKey->table_name))) {
                                                        // 検索結果からの戻り（項目検索）
                                                        $selectedValue = 'item_' . current(Yii::$app->request->post($searchKey->table_name));
                                                        $getCategoryItemsList['list'] = array_merge($getCategoryItemsList['list'], [$selectedValue => ['selected' => true]]);
                                                    } else {
                                                        // Topからの引継ぎ
                                                        $selectedValue = Yii::$app->request->post($searchKey->table_name);
                                                        if (strpos($selectedValue, 'cate') !== false) {
                                                            $getCategoryItemsList['list'] = array_merge($getCategoryItemsList['list'], [$selectedValue => ['selected' => true, 'style' => 'font-weight: bold;']]);
                                                        } else {
                                                            $getCategoryItemsList['list'] = array_merge($getCategoryItemsList['list'], [$selectedValue => ['selected' => true]]);
                                                        }
                                                    }

                                                    echo Html::dropDownList(
                                                        $searchKey->table_name,
                                                        (array)$searchForm->{$childAttribute},
                                                        $getCategoryItemsList['results'],
                                                        [
                                                            'class' => 'form-control',
                                                            'prompt' => '---',
                                                            'options' => $getCategoryItemsList['list'],
                                                        ]
                                                    );
                                                } else {
                                                $getCategoryOptions = $searchForm->getCategoryOptions($searchKey->categories);
                                                echo Html::dropDownList(
                                                    $searchKey->table_name,
                                                    (array)$searchForm->{$childAttribute},
                                                    $getCategoryOptions,
                                                    [
                                                        'class' => 'form-control',
                                                        'prompt' => '---',
                                                    ]
                                                );
                                                } ?>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <div class="check-field clearfix">
                                        <div class="category-<?= $searchKey->table_name ?>">
                                            <!-- 選択中の項目-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

            <?php elseif ($searchKey->isItem && $searchKey->items):
                $attribute = "searchkey_item{$searchKey->itemId}";
                $formName = "{$attribute}_string";
                ?>

                <tr>
                    <th><?= Html::encode($searchKey->searchkey_name); ?></th>
                    <td>
                        <div class="s-select-overlay-only-block js-select-overlay-only">
                            <div class="select-overlay-only">
                                <div class="select-overlay-only-list op-float">
                                    <?php if ($searchKey->search_input_tool == SearchkeyMaster::SEARCH_INPUT_TOOL_MODAL): ?>
                                        <?php
                                        // 小項目がカンマ区切りで入るhidden
                                        echo Html::hiddenInput(
                                            $formName,
                                            implode(',', (array)$searchForm->{$attribute}),
                                            ['id' => "hidden-{$attribute}"]
                                        );
                                        ?>

                                        <?= $this->render('@app/views/kyujin/search-form/_item-modal', [
                                            'searchForm' => $searchForm,
                                            'allCount' => $allCount ?? null,
                                            'hierarchyFirst' => $attribute,
                                            'searchKey' => $searchKey,
                                            'identifier' => $attribute,
                                        ]); ?>
                                        <?= Html::a(
                                            Html::tag(
                                                'span',
                                                '',
                                                [
                                                    'class' => [
                                                        'fa',
                                                        'fa-plus',
                                                    ],
                                                ]
                                            ) . Yii::t('app', '選択する'),
                                            '#search-modal-' . $searchKey->table_name,
                                            [
                                                'class' => 'mod-btn9 js-modal-open',
                                                'data-toggle' => 'modal',
                                            ]
                                        ); ?>

                                    <?php elseif ($searchKey->search_input_tool == SearchkeyMaster::SEARCH_INPUT_TOOL_CHECKBOX): ?>

                                        <div class="check-field clearfix">
                                            <ul id="item-<?= Html::encode($searchKey->table_name); ?>-group"
                                                class="clearfix searchCate_<?= $searchKey->tableName() ?>">
                                                <?php foreach ($searchKey->items as $i => $item): ?>
                                                    <li>
                                                        <?= Html::input(
                                                            'checkbox',
                                                            $searchKey->table_name . '[]',
                                                            $item->searchkey_item_no,
                                                            [
                                                                'id' => "{$searchKey->table_name}-{$item->id}-{$i}",
                                                                'checked' => in_array(
                                                                    $item->searchkey_item_no,
                                                                    (array)$searchForm->{$attribute}
                                                                ),
                                                            ]
                                                        ); ?>
                                                        <?= Html::tag(
                                                            'label',
                                                            $item->searchkey_item_name,
                                                            [
                                                                'class' => 'pref',
                                                                'for' => "{$searchKey->table_name}-{$item->id}-{$i}",
                                                            ]
                                                        ) ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>

                                    <?php else: ?>

                                        <div class="s-form-group-list">
                                            <div class="form-group">
                                                <?= Html::dropDownList(
                                                    $searchKey->table_name,
                                                    (array)$searchForm->{$attribute},
                                                    ArrayHelper::map(
                                                        $searchKey->items,
                                                        'searchkey_item_no',
                                                        'searchkey_item_name'
                                                    ),
                                                    [
                                                        'class' => 'form-control',
                                                        'prompt' => '---',
                                                    ]
                                                ); ?>
                                            </div>
                                        </div>

                                    <?php endif; ?>

                                    <div class="check-field clearfix">
                                        <div class="<?= $attribute ?>">
                                            <!-- 選択中の項目-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

            <?php endif; ?>

        <?php endforeach; ?>

    </table>
</div>
