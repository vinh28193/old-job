<?php
/**
 * 駅検索オーバーレイ
 * 都道府県ごとのajax表示部分
 */
use app\models\forms\JobSearchForm;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\Station;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/* @var $this       View */
/* @var $searchForm JobSearchForm */
/* @var $pref       Pref */

Pjax::begin([
    'enablePushState' => false,
    'options'         => [
        'id'    => 'ajaxStation',
        'class' => 's-overlay-selected-category-block js-overlay-content-move',
    ],
]);

$identifier = 'station'; // オーバーレイ識別子
// todo モデルから取得できるようにする
$companies = ArrayHelper::index(Station::findAll(['pref_no' => $pref->pref_no]) ?? [], null, ['railroad_company_cd', 'route_cd']);
?>
<div class="overlay-selected-category s-select-area-block">

    <div class="c-title-label op-mb0">
        <div class="title"><?= Html::encode($pref->pref_name); ?></div>
    </div>

    <?php foreach ($companies as $companyCd => $routes): ?>
        <?php foreach ($routes as $routeCd => $stations): ?>
            <div class="c-selected-category-list js-selected-category in-overlay">
                <div class="c-selected-category-title-label">

                    <div class="left">
                        <h2 class="title" data-parent-id="<?= Html::encode($routeCd); ?>">
                            <?= Html::encode(current($stations)->route_name); ?>
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

                            <?php /** @var Station[] $stations */
                            foreach ($stations as $i => $station): ?>
                                <?= Html::tag(
                                    'li',
                                    Html::input(
                                        'checkbox',
                                        'btn',
                                        $station->station_no,
                                        [
                                            'checked' => in_array($station->station_no, (array)$searchForm->station) ||
                                                in_array($station->route_cd, (array)$searchForm->station_parent),
                                        ]
                                    ) .
                                    Html::tag('label', Html::encode($station['station_name'])),
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
        <?php endforeach; ?>
    <?php endforeach; ?>

</div>
<?php Pjax::end() ?>
