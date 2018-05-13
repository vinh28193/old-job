<?php
/**
 * 1階層検索モーダル
 */
use yii\helpers\Html;

/* @var $searchKey \app\models\JobSearch */
/* @var $allCount integer */
/* @var $hierarchyFirst string */
$attribute = "searchkey_item{$searchKey->itemId}";
?>
<div class="modal fade" id="search-modal-<?= Html::encode($searchKey->table_name); ?>" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!--▼モーダルヘッダー▼-->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only"><?= Yii::t('app', '閉じる') ?></span>
                </button>
                <h4 class="mod-h2" id="myModalLabel">
                    <?= Yii::t('app', '{LABEL}で探す', ['LABEL' => Html::encode($searchKey->searchkey_name)]) ?>
                </h4>
            </div>
            <!--▲モーダルヘッダー▲-->

            <!--▼モーダルボディ▼-->
            <div class="modal-body">
                <div class="tab_container">
                    <!--▼モーダルコンテンツ▼-->
                    <div class="search-container">
                        <div class="tab-pane active">
                            <!--条件検索-->
                            <div class="check-field clearfix">

                                <!--小項目-->
                                <ul id="item-<?= Html::encode($searchKey->table_name); ?>-group"
                                    class="collapse in clearfix">
                                    <?php
                                    foreach ($searchKey->items as $item):
                                        $itemId = $searchKey->table_name . '-item-' . $item->searchkey_item_no;
                                        ?>
                                        <?= Html::tag(
                                        'li',
                                        Html::input(
                                            'checkbox',
                                            'check',
                                            $item->searchkey_item_no,
                                            [
                                                'id'          => $itemId,
                                                'class'       => $hierarchyFirst,
                                                'data-toggle' => 'switch',
                                                'checked'     => in_array(
                                                    $item->searchkey_item_no,
                                                    (array)$searchForm->{$attribute}
                                                ),
                                            ]
                                        ) .
                                        Html::tag(
                                            'label',
                                            Html::encode($item->searchkey_item_name),
                                            [
                                                'for' => $itemId,
                                            ]
                                        )
                                    ); ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--▲モーダルコンテンツ▲-->
            </div>
            <!--▲モーダルボディ▲-->

            <!--▼モーダルフッター▼-->
            <div class="modal-footer">
                <div class="btn-group">
                    <div class="btn-group__right">
                        <?= Html::tag(
                            'button',
                            Yii::t('app', 'チェックを外す'),
                            [
                                'class'        => [
                                    'mod-btn3',
                                    'btn-group__left',
                                    '',
                                ],
                                'id'           => 'area_set',
                                'type'         => 'button',
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
                                'class'            => [
                                    'mod-btn2',
                                    'btn-group__right',
                                    'items-decision',
                                    'to-opener',
                                ],
                                'name'             => 'submit_btn',
                                'value'            => 'search',
                                'type'             => 'button',
                                'data-dismiss'     => 'modal',
                                'data-target'      => $identifier,
                                'data-hierarchies' => $hierarchyFirst,
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <!--▲モーダルフッター▲-->

        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal -->
