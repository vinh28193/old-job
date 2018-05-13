<?php
/**
 * ピックアップ検索モーダル
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;

/* @var $searchForm JobSearchForm */
/* @var $allCount integer */
/* @var $identifier string */
/* @var $hierarchyFirst string */
/* @var $hierarchySecond string */

// スマホ優先キー
$principalKey = $searchForm->principalKey;
// スマホ優先キーの親の名前
$principalChild = $principalKey->table_name;
// スマホ優先キーの子の名前
$principalParent = $principalChild . '_parent';
?>
<!-- 職種モーダル///////////////////////////////////////////////////////////////////////////////////////////////////-->
<!-- モーダルにて表示されるボックスの設定-->
<div class="modal fade" id="search-modal-job" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- ▼モーダルヘッダー▼-->
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only"><?= Yii::t('app', '閉じる'); ?></span>
                </button>
                <h4 class="mod-h2" id="myModalLabel">
                    <?= Html::encode($searchForm->principalKey->searchkey_name); ?><?= Yii::t('app', 'で探す'); ?>
                </h4>
            </div>
            <!-- ▲モーダルヘッダー▲-->
            <!-- ▼モーダルボディ▼-->
            <div class="modal-body">
                <!-- ▼モーダルコンテンツ▼-->
                <div class="search-container">
                    <div class="tab-pane active"
                         id="tab-pane-<?= Html::encode($searchForm->principalKey->table_name); ?>">
                        <?php foreach ($searchForm->principalCategories as $category): ?>
                            <?php if ($category->items): ?>
                                <?php
                                $principalId = 'principal-category-' . $category->searchkey_category_no;
                                $firstParentId = $hierarchyFirst . '-' . $category->searchkey_category_no;
                                ?>
                                <div class="firstLayer">
                                    <div class="check-field clearfix">
                                        <!-- ▼職種タイトル▼-->
                                        <div class="mod-checkItem-title top-title">
                                            <div class="checkItem">
                                                <?= Html::input(
                                                    'checkbox',
                                                    'check',
                                                    $category->searchkey_category_no,
                                                    [
                                                        'id' => $principalId . '-for',
                                                        'class' => $hierarchyFirst,
                                                        'data-toggle' => 'switch',
                                                        'data-target' => "#{$principalId}",
                                                        'checked' => in_array(
                                                            $category->searchkey_category_no,
                                                            (array)$searchForm->{$principalParent}
                                                        ),
                                                    ]
                                                ); ?>
                                                <label for="<?= $principalId . '-for'; ?>">
                                                    <?= Html::encode($category->searchkey_category_name); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <!-- ▲職種タイトル▲-->
                                        <ul class="collapse in clearfix" id="<?= $principalId; ?>">

                                            <?php
                                            foreach ($category->items as $item):
                                                $itemId = 'principal-item-' . $item->searchkey_item_no;
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
                                                        'data-parent' => "#{$principalId}",
                                                        'data-parent-ids' => $firstParentId,
                                                        'data-to-list' => $identifier,
                                                        'checked' => in_array(
                                                            $item->searchkey_item_no,
                                                            (array)$searchForm->{$principalChild}
                                                        ),
                                                    ]
                                                ) .
                                                Html::tag('label', Html::encode($item->searchkey_item_name),
                                                    [
                                                        'class' => 'pref',
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
                                'data-hierarchies' => implode(
                                    ' ',
                                    [
                                        $hierarchyFirst,
                                        $hierarchySecond,
                                    ]
                                ),
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
