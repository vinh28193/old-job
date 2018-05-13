<?php
/**
 * 駅検索モーダル
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;

/* @var $searchForm JobSearchForm */
/* @var $allCount integer */
/* @var $station \app\models\manage\searchkey\Station */
/* @var $identifier string */
/* @var $otherIdentifier string */
/* @var $hierarchyFirst string */
/* @var $hierarchySecond string */

$stationParts = $searchForm->getStationParts();
?>
<div class="modal fade in" id="search-modal-railway" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- ▼モーダルヘッダー▼-->
            <div class="modal-header">
                <button class="close" type="button" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only"><?= Yii::t('app', '閉じる'); ?></span>
                </button>
                <h4 class="mod-h2" id="myModalLabel"><?= Yii::t('app', '勤務地で探す'); ?></h4>
            </div>
            <!-- ▲モーダルヘッダー▲-->
            <!-- ▼モーダルボディ▼-->
            <div class="modal-body">
                <div class="tab_container">
                    <!-- ▼モーダルコンテンツ▼-->
                    <div class="search-container tab-content">
                        <!-- ▼タブナビ▼-->
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab" href="#search-railway"><?= Yii::t('app', '駅で探す'); ?></a>
                            </li>
                        </ul>
                        <!-- ▼ 駅で探すタブ ▼-->
                        <div class="tab-pane active in" id="search-railway">
                            <div class="row mod-checkBoxes">
                                <!-- ▼都道府県▼-->
                                <div class="col-xs-3 col-md-3">
                                    <ul class="mod-checkBoxes__group mod-switchBox">

                                        <?php foreach ($stationParts as $stationPrefId => $railroadCompany): ?>
                                            <?= Html::tag(
                                                'li',
                                                Html::a(
                                                    Html::encode($searchForm->getPrefNameById($stationPrefId)),
                                                    '#'
                                                ) .
                                                Html::tag('span', '', [
                                                    'class' => 'fa',
                                                ]),
                                                [
                                                    'class'       => [
                                                        'item',
//                                                        'active',
                                                    ],
                                                    'data-target' => 'rail-' . $stationPrefId,
                                                ]
                                            ); ?>
                                        <?php endforeach; ?>

                                    </ul>
                                </div>

                                <?php
                                foreach ($stationParts as $stationPrefId => $railroadCompany):
                                    $prefId = 'rail-pref-' . $stationPrefId;
                                    ?>
                                    <div class="mod-checkBoxes__check-group col-xs-9 col-md-9" id="rail-<?= $stationPrefId; ?>">
                                        <div class="check-field clearfix">
                                            <!-- 都道府県-->
                                            <!-- ▼都道府県タイトル▼-->
                                            <div class="mod-checkItem-title top-title">
                                                <div class="checkItem">
                                                    <label for="<?= $prefId; ?>">
                                                        <?= Html::encode($searchForm->getPrefNameById($stationPrefId)); ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- ▲都道府県タイトル▲ --->

                                            <?php foreach ($railroadCompany as $railroadCompanyCd => $route): ?>
                                                <div class="mod-checkItem-title">
                                                    <div class="checkItem">
                                                        <label>
                                                            <?= Html::encode($searchForm->getRailroadNameById($railroadCompanyCd)); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <!-- ▼沿線▼-->
                                                <?php
                                                foreach ($route as $routeCd => $stations):
                                                    $routeId = 'pref-' . $stationPrefId . '_rail-route-' . $routeCd;
                                                    $firstParentId = $hierarchyFirst . '-' . $routeCd;
                                                    ?>
                                                    <div class="mod-checkItem-title sub-title state-icon">
                                                        <div class="checkItem <?= $prefId; ?>">
                                                            <?= Html::input(
                                                                'checkbox',
                                                                'check',
                                                                $routeCd,
                                                                [
                                                                    'id'          => $routeId . '-for',
                                                                    'class'       => $hierarchyFirst,
                                                                    'data-toggle' => 'switch',
                                                                    'data-target' => '#' . $routeId,
                                                                    'checked'     => in_array(
                                                                        $routeCd,
                                                                        (array)$searchForm->station_parent
                                                                    ),
                                                                ]
                                                            ); ?>
                                                            <label for="<?= $routeId . '-for' ?>">
                                                                <?= $searchForm->getRouteNameById($routeCd) ?>
                                                            </label>
                                                        </div>
                                                        <div class="checkItem-js collapsed" data-toggle="collapse" data-target="#<?=$routeId?>">
                                                            <span class="fa fa-chevron-down"></span>
                                                        </div>
                                                    </div>
                                                    <ul class="collapse clearfix <?= $prefId; ?>" id="<?= $routeId; ?>">
                                                        <?php
                                                        foreach ($stations as $station):
                                                            $stationId = join('', ['pref-', $stationPrefId, '_railway-', $routeCd, '_station-', $station->station_no]);
                                                            ?>
                                                            <li>
                                                                <?= Html::input(
                                                                    'checkbox',
                                                                    'check',
                                                                    $station->station_no,
                                                                    [
                                                                        'id'              => $stationId,
                                                                        'class'           => $hierarchySecond,
                                                                        'data-toggle'     => 'switch',
                                                                        'data-to-list'    => $identifier,
                                                                        'data-parent'     => '#' . $routeId,
                                                                        'data-parent-ids' => $firstParentId,
                                                                        'checked'         => in_array(
                                                                            $station->station_no,
                                                                            (array)$searchForm->station
                                                                        ),
                                                                    ]
                                                                ); ?>
                                                                <label class="pref" for="<?= $stationId; ?>">
                                                                    <?= Html::encode($station->station_name); ?>
                                                                </label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>

                                                <?php endforeach; ?>
                                                <!-- ▲沿線▲-->
                                            <?php endforeach; ?>

                                        </div>
                                    </div>
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
                                    'has-left-pane',
                                ],
                                'name'             => 'submit_btn',
                                'value'            => 'search',
                                'type'             => 'button',
                                'data-dismiss'     => 'modal',
                                'data-target'      => $identifier,
                                'data-other'       => $otherIdentifier,
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
                <!-- ▲モーダルフッター▲-->
            </div>
        </div>
        <!-- /.modal-content-->
    </div>
    <!-- /.modal-dialog-->
</div>
