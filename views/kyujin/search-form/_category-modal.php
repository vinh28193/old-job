<?php
/**
 * 2階層検索モーダル
 */
use yii\helpers\Html;

/* @var $searchKey \app\models\JobSearch */
/* @var $allCount integer */
/* @var $hierarchyFirst string */
/* @var $hierarchySecond string */

$parentAttribute = "searchkey_category{$searchKey->categoryId}_parent";
$childAttribute = "searchkey_category{$searchKey->categoryId}";
?>
<!-- 2階層モーダル///////////////////////////////////////////////////////////////////////////////////////////////////-->
<!-- モーダルにて表示されるボックスの設定-->
<div class="modal fade" id="search-modal-<?= $searchKey->table_name; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- ▼モーダルヘッダー▼-->
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal">
                    <span aria-hidden="true">×</span><span class="sr-only"><?= Yii::t('app', '閉じる'); ?></span>
                </button>
                <h4 class="mod-h2" id="myModalLabel">
                    <?= Html::encode($searchKey->searchkey_name); ?><?= Yii::t('app', 'で探す'); ?>
                </h4>
            </div>
            <!-- ▲モーダルヘッダー▲-->
            <!-- ▼モーダルボディ▼-->
            <div class="modal-body">
                <!-- ▼モーダルコンテンツ▼-->
                <div class="search-container">
                    <div class="tab-pane active" id="tab-pane-<?= Html::encode($searchKey->table_name); ?>">

                        <?php foreach ($searchKey->categories as $category): ?>
                            <?php if ($category->items): ?>
                                <?php $categoryId = $searchKey->table_name . '-category-' . $category->searchkey_category_no;
                                $firstParentId = $hierarchyFirst . '-' . $category->searchkey_category_no;
                                ?>
                                <div class="firstLayer">
                                    <div class="check-field clearfix">
                                        <!-- ▼カテゴリラベル▼-->
                                        <div class="mod-checkItem-title top-title">
                                            <div class="checkItem">
                                                <?php if ($searchKey->is_category_label != 1): ?>
                                                    <?= Html::input(
                                                        'checkbox',
                                                        'check',
                                                        $category->searchkey_category_no,
                                                        [
                                                            'id' => $categoryId,
                                                            'class' => $hierarchyFirst,
                                                            'data-toggle' => 'switch',
                                                            'data-target' => '.' . $categoryId,
                                                            'checked' => in_array(
                                                                $category->searchkey_category_no,
                                                                (array)$searchForm->{$parentAttribute}
                                                            ),
                                                        ]
                                                    ); ?>
                                                <?php endif; ?>
                                                <label for="<?= $categoryId; ?>">
                                                    <?= Html::encode($category->searchkey_category_name); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- ▲カテゴリラベル▲-->
                                        <ul class="collapse in clearfix <?= $categoryId; ?>">

                                            <?php
                                            foreach ($category->items as $item):
                                                $itemId = $searchKey->table_name . '-item-' . $item->searchkey_item_no;
                                                ?>
                                                <?= Html::tag(
                                                'li',
                                                Html::input(
                                                    'checkbox',
                                                    'check',
                                                    $item->searchkey_item_no,
                                                    [
                                                        'id' => $itemId,
                                                        'class' => $hierarchySecond,
                                                        'data-toggle' => 'switch',
                                                        'data-parent' => ".{$categoryId}",
                                                        'data-parent-ids' => $firstParentId,
                                                        'checked' => in_array(
                                                            $item->searchkey_item_no,
                                                            (array)$searchForm->{$childAttribute}
                                                        ),
                                                    ]
                                                ) .
                                                Html::tag(
                                                    'label',
                                                    Html::encode($item->searchkey_item_name),
                                                    [
                                                        'for' => $itemId,
                                                    ]
                                                )); ?>
                                            <?php endforeach; ?>

                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                    </div>
                </div>
                <!-- ▲モーダルコンテンツ▲-->
            </div>
            <!-- ▲モーダルボディ▲-->
            <!-- ▼モーダルフッター▼-->
            <div class="modal-footer">
                <div class="btn-group">
                    <div class="btn-group__right">
                        <?= Html::tag(
                            'button',
                            Yii::t('app', 'チェックを外す'),
                            [
                                'class' => [
                                    'mod-btn3',
                                    'btn-group__left',
                                    '',
                                ],
                                'id' => 'area_set',
                                'type' => 'button',
                            ]
                        ); ?>
                        <?= Html::tag(
                            'button',
                            Yii::t('app', '確定') . Html::tag(
                                'span',
                                Html::encode(($allCount ?? null) ? "({$allCount})" : ''),
                                [
                                    'class' => 'inner-num',
                                ]
                            ),
                            [
                                'class' => [
                                    'mod-btn2',
                                    'btn-group__right',
                                    'items-decision',
                                    'to-opener',
                                ],
                                'name' => 'submit_btn',
                                'value' => 'search',
                                'type' => 'button',
                                'data-dismiss' => 'modal',
                                'data-target' => $identifier,
                                'data-hierarchies' => implode(' ', [$hierarchyFirst, $hierarchySecond]),
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <!-- ▲モーダルフッター▲-->
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
<!-- /.modal-->
