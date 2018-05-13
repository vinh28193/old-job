<?php
/**
 * 地域検索モーダル
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;

/* @var $searchForm JobSearchForm */
/* @var $allCount integer */
/* @var $identifier string */
/* @var $otherIdentifier string */
/* @var $hierarchyFirst string */
/* @var $hierarchySecond string */
/* @var $hierarchyThird string */
?>
<!-- エリアモーダル///////////////////////////////////////////////////////////////////////////////////////////////////-->
<!-- モーダルにて表示されるボックスの設定-->
<div class="modal fade in" id="search-modal-area" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- ▼モーダルヘッダー▼-->
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal">
                    <span aria-hidden="true">×</span><span class="sr-only"><?= Yii::t('app', '閉じる'); ?></span>
                </button>
                <h4 class="mod-h2" id="myModalLabel"><?= Yii::t('app', '勤務地で探す'); ?></h4>
            </div>
            <!-- ▲モーダルヘッダー▲-->
            <!-- ▼モーダルボディ▼-->
            <div class="modal-body">
                <!-- ▼タブナビ▼-->
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#search-area"><?= Yii::t('app', 'エリアで探す'); ?></a>
                    </li>
                </ul>
                <!-- ▲タブナビ▲-->
                <div class="tab_container">
                    <!-- ▼モーダルコンテンツ▼-->
                    <div class="search-container tab-content">
                        <!-- ▼タブコンテンツ▼-->
                        <!-- ▼地域で選ぶ▼-->
                        <div class="tab-pane active in" id="search-area">
                            <div class="row mod-checkBoxes">
                                <!-- ▼都道府県▼-->
                                <div class="col-xs-3 col-md-3">
                                    <ul class="mod-checkBoxes__group mod-switchBox">

                                        <?php foreach ($searchForm->prefs as $pref): ?>
                                            <?php if ($pref->prefDistMasters): ?>
                                                <?= Html::tag(
                                                    'li',
                                                    Html::a(
                                                        Html::encode(Yii::t('app', $pref->pref_name)),
                                                        'javascript:void(0)'
                                                    ) .
                                                    Html::tag('span', '', [
                                                        'class' => 'fa',
                                                    ]),
                                                    [
                                                        'class' => [
                                                            'item',
                                                        ],
                                                        'data-target' => 'area-' . $pref->pref_no,
                                                    ]
                                                ); ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                    </ul>
                                </div>
                                <!-- ▲都道府県▲-->

                                <?php foreach ($searchForm->prefs as $pref): ?>
                                    <?php if ($pref->prefDistMasters): ?>
                                        <?php
                                        $prefId = 'area-pref-' . $pref->pref_no;
                                        $distParentId = $prefId . '-parent';
                                        ?>
                                        <div class="mod-checkBoxes__check-group col-xs-9 col-md-9"
                                             id="area-<?= $pref->pref_no; ?>">
                                            <!-- エリア検索-->
                                            <div class="check-field clearfix">
                                                <!-- ▼都道府県タイトル▼-->
                                                <div class="mod-checkItem-title top-title">
                                                    <div class="checkItem">
                                                        <?= Html::input(
                                                            'checkbox',
                                                            'check',
                                                            $pref->pref_no,
                                                            [
                                                                'id' => $distParentId,
                                                                'class' => $hierarchyFirst,
                                                                'data-toggle' => 'switch',
                                                                'data-target' => '.' . $prefId,
                                                                'data-parents' => "#{$prefId}",
                                                                'checked' => in_array(
                                                                    $pref->pref_no,
                                                                    (array)$searchForm->pref
                                                                ),
                                                            ]
                                                        ) ?>
                                                        <label for="<?= $distParentId; ?>">
                                                            <?= Html::encode($pref->pref_name); ?>
                                                            <?= Yii::t('app', 'すべて'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <!-- ▲都道府県タイトル▲ --->

                                                <?php foreach ($pref->dispPrefDistMasters as $prefDist): ?>
                                                    <?php if ($prefDist->valid_chk && $prefDist->districts): ?>
                                                        <?php
                                                        $prefDisId = 'area-pref-dist-' . $prefDist->pref_dist_master_no;
                                                        $prefDisIdFor = $prefDisId . '-for';
                                                        $firstParentId = $hierarchyFirst . '-' . $pref->pref_no;
                                                        ?>
                                                        <!-- ▼地域タイトル▼-->
                                                        <div class="mod-checkItem-title">
                                                            <div
                                                                class="checkItem <?= $prefId; ?> <?= $distParentId; ?>">
                                                                <?= Html::input(
                                                                    'checkbox',
                                                                    'check',
                                                                    $prefDist->pref_dist_master_no,
                                                                    [
                                                                        'id' => $prefDisIdFor,
                                                                        'class' => $hierarchySecond,
                                                                        'data-toggle' => 'switch',
                                                                        'data-target' => '#' . $prefDisId,
                                                                        'data-parent' => ".{$prefId}",
                                                                        'data-parent-ids' => $firstParentId,
                                                                        'checked' => in_array(
                                                                            $prefDist->pref_dist_master_no,
                                                                            (array)$searchForm->pref_dist_master_parent
                                                                        ),
                                                                    ]
                                                                ) ?>
                                                                <label for="<?= $prefDisIdFor; ?>">
                                                                    <?= Html::encode($prefDist->pref_dist_name); ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <!-- ▲地域タイトル▲-->

                                                        <!-- 市区町村-->
                                                        <ul class="collapse in clearfix <?= $prefId; ?>"
                                                            id="<?= $prefDisId; ?>">

                                                            <?php foreach ($prefDist->districts as $dist): ?>
                                                                <?php $areaDistId = 'area-dist-' . $dist->dist_cd; ?>
                                                                <?= Html::tag(
                                                                    'li',
                                                                    Html::input(
                                                                        'checkbox',
                                                                        'check',
                                                                        $dist->dist_cd,
                                                                        [
                                                                            'id' => $areaDistId,
                                                                            'class' => $hierarchyThird,
                                                                            'data-toggle' => 'switch',
                                                                            'data-to-list' => $identifier,
                                                                            'data-parent' => "#{$prefDisId}",
                                                                            'data-parent-ids' => implode(
                                                                                ' ',
                                                                                [
                                                                                    $firstParentId,
                                                                                    $hierarchySecond . '-' . $prefDist->pref_dist_master_no,
                                                                                ]
                                                                            ),
                                                                            'checked' => in_array(
                                                                                $dist->dist_cd,
                                                                                (array)$searchForm->pref_dist_master
                                                                            ),
                                                                        ]
                                                                    ) .
                                                                    Html::tag(
                                                                        'label',
                                                                        Html::encode($dist->dist_name),
                                                                        [
                                                                            'for' => $areaDistId,
                                                                        ]
                                                                    )
                                                                ); ?>
                                                            <?php endforeach; ?>

                                                        </ul>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>

                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>
                    <!-- ▲モーダルコンテンツ▲-->
                </div>
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
                                ],
                                'id' => 'area_set',
                                'type' => 'button',
                            ]
                        ); ?>
                        <?= Html::tag(
                            'button',
                            Yii::t('app', '確定') . Html::tag(
                                'span',
                                Html::encode(($allcount ?? null) ? "({$allcount})" : ''),
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
                                    'has-left-pane',
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
                                        $hierarchyThird,
                                    ]
                                ),
                                'data-other' => $otherIdentifier,
                            ]
                        ); ?>
                    </div>
                </div>
                <!-- ▲モーダルフッター▲-->
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
