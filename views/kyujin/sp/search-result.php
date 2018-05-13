<?php
/**
 * SP用検索結果View
 */

use app\common\widget\DropDownSorter;
use app\models\forms\JobSearchForm;
use app\models\manage\CustomField;
use app\models\manage\NameMaster;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\BootstrapPluginAsset;
use yii\widgets\ListView;

BootstrapPluginAsset::register($this);

/* @var $this View */
/* @var $searchForm JobSearchForm */
/* @var $dataProvider ActiveDataProvider */
/* @var $conditions array 検索queryパラメータ */
/* @var $breadcrumbs array パン屑のlinkに使う配列 */

$this->params['keep'] = true;
// スマホ優先キー
$principalKey = $searchForm->principalKey;
// スマホ優先キーの親の名前
$principalChild = $principalKey->table_name ?? '';
// スマホ優先キーの子の名前
$principalParent = $principalChild . '_parent';
// エリアナンバー
$area = ArrayHelper::remove($conditions, 'area', null);
// 路線コード
$stationParents = ArrayHelper::remove($conditions, 'station_parent', []);
// 駅コード
$stations = ArrayHelper::remove($conditions, 'station', []);
// 都道府県ナンバー
$prefs = ArrayHelper::remove($conditions, 'pref', []);
// 地域グループナンバー
$prefDistMasterParents = ArrayHelper::remove($conditions, 'pref_dist_master_parent', []);
// 市区町村コード
$prefDistMasters = ArrayHelper::remove($conditions, 'pref_dist_master', []);
// 優先キーカテゴリーナンバー
$principalParents = ArrayHelper::remove($conditions, $principalParent, []);
// 優先キー項目ナンバー
$principalChildren = ArrayHelper::remove($conditions, $principalChild, []);
// キーワードを除外
ArrayHelper::remove($conditions, 'keyword', '');
// パンくずを設定
$this->params['breadcrumbs'] = $breadcrumbs;

$prefJson                 = Json::encode($prefs);
$prefDistMasterParentJson = Json::encode($prefDistMasterParents);
$prefDistMasterJson       = Json::encode($prefDistMasters);
$stationParentJson        = Json::encode($stationParents);
$stationJson              = Json::encode($stations);

$nameJsonJs = <<<JS
var prefs = {$prefJson};
var prefDistMasterParents = {$prefDistMasterParentJson};
var prefDistMasters = {$prefDistMasterJson};
var stationParents = {$stationParentJson};
var stations = {$stationJson};

function appendListItem(itemId, text) {
    // フッタ選択中
    $('.selecting-list-item .list-item').append(
        '<span class="' + itemId + '">' + text + '</span>'
    );
}

$.each(prefs, function(text, value) {
    var target= 'pref';
    appendListItem(target + '-' + value, text);
});
$.each(prefDistMasterParents, function(text, value) {
    var target= 'pref_dist_master-parent';
    appendListItem(target + '-' + value, text);
});
$.each(prefDistMasters, function(text, value) {
    var target= 'pref_dist_master';
    appendListItem(target + '-' + value, text);
});
$.each(stationParents, function(text, value) {
    var target= 'station-parent';
    appendListItem(target + '-' + value, text);
});
$.each(stations, function(text, value) {
    var target= 'station';
    appendListItem(target + '-' + value, text);
});
$('.detail-item').each(function() {
    appendListItem('', $(this).text());
});
JS;
$this->registerJs($nameJsonJs);
?>

<?= $this->render('search/_area-overlay', [
    'searchForm'            => $searchForm,
    'prefs'                 => $prefs,
    'prefDistMasterParents' => $prefDistMasterParents,
    'prefDistMasters'       => $prefDistMasters,
]); ?>

<?= $this->render('search/_station-overlay', [
    'searchForm'     => $searchForm,
    'stationParents' => $stationParents,
    'stations'       => $stations,
]); ?>

<?php if ($searchForm->principalKey): ?>
    <?= $this->render('search/_principal-overlay', [
        'searchForm' => $searchForm,
    ]); ?>
<?php endif; ?>
<!-- Main Contents ===============================================-->
<!-- Container ===========================-->
<!--▼検索ボックス▼-->　
<div class="s-job-result-selected-result-block">
    <div class="job-result-selected-result">

        <?php if ((!$searchForm->hasDetailSearchParams()) && $searchForm->keyword): // 詳細検索パラメータが無いかつフリーワード検索パラメータがある場合のみフリーワードを表示?>
            <!--▼フリーワード検索▼-->
            <div class="s-job-result-selected-result-search-item-block">
                <div class="search-item-item-box-default">
                    <div class="search-item-item-box">
                        <div class="left">
                            <div class="s-btn-radio-item-block">
                                <div class="btn-radio-item js-omit-select-btn">
                                    <?php foreach ($searchForm->keywords as $word): ?>
                                        <?= Html::tag('span', $word, ['class' => 'c-btn-radio-item']) ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="right">
                            <div class="s-btn-selet-change-block">
                                <?= Html::a(Yii::t('app', '変更'), 'javascript:void(0)',
                                    ['class' => 'c-btn op-link op-select js-search-select-free-word-trigger-on']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="select-detail-link-block">
                        <?= Html::a(Yii::t('app', '詳細検索に変更'), Url::to('/kyujin/search-detail')) ?>
                    </div>
                </div>
                <?= Html::beginForm('/search-result'); ?>
                <?= Html::hiddenInput('area', $area); ?>
                <div class="search-item-free-word-box js-show-toggle">
                    <div class="s-search-select-free-word-block">
                        <div class="left">
                            <?= Html::textInput('keyword', null, ['class' => 'c-input-text']) ?>
                        </div>
                        <div class="right">
                            <?= Html::submitButton(Yii::t('app', '検索'), ['class' => 'c-input-btn-submit']) ?>
                        </div>
                    </div>
                </div>
                <?= Html::endForm(); ?>
            </div>
            <!--▲フリーワード検索▲-->
        <?php else: // 両方ある場合や両方ない場合は詳細検索を優先して表示 ?>
            <?= Html::beginForm('/kyujin/search-detail', 'post', ['id' => 'searchForm']); ?>
            <?= Html::hiddenInput('area', $area); ?>
            <!--▼地域or駅・優先キー検索▼-->
            <div class="s-job-result-selected-result-search-item-block">
                <div class="search-item-title">
                    <div class="c-title-notes"><?= Yii::t('app', '検索条件') ?></div>
                </div>
                <?php if ($searchForm->pref_dist_master === null && $searchForm->station !== null): // 地域が無いかつ駅がある場合のみ駅を表示?>
                    <!--▼駅検索▼-->
                    <div class="search-item-item-box">
                        <div class="left">
                            <div class="s-btn-radio-item-block">
                                <div class="btn-radio-item js-omit-select-btn">
                                    <?php foreach ($stationParents as $name => $no): ?>
                                        <?= Html::tag('span', $name, ['class' => 'c-btn-radio-item']) ?>
                                    <?php endforeach; ?>
                                    <?php foreach ($stations as $name => $no): ?>
                                        <?= Html::tag('span', $name, ['class' => 'c-btn-radio-item']); ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="right">
                            <div class="s-btn-selet-change-block">
                                <?= Html::a(Yii::t('app', '{station}を変更',
                                    ['station' => $searchForm->searchKeys['station']->searchkey_name] ?? ''),
                                    '#ovl-train',
                                    ['class' => 'c-btn op-link op-select js-overlay-trigger-on']) ?>
                            </div>
                        </div>
                    </div>
                    <!--▲駅検索▲-->
                <?php else: // 地域も駅も無い場合や両方ある場合は地域を優先して表示?>
                    <!--▼地域検索▼-->
                    <div class="search-item-item-box">
                        <div class="left">
                            <div class="s-btn-radio-item-block">
                                <?php if ($prefs || $prefDistMasterParents || $prefDistMasters): ?>
                                    <div class="btn-radio-item js-omit-select-btn">
                                        <?php foreach ($prefs as $name => $no): ?>
                                            <?= Html::tag('span', $name, ['class' => 'c-btn-radio-item']) ?>
                                        <?php endforeach; ?>
                                        <?php foreach ($prefDistMasterParents as $name => $no): ?>
                                            <?= Html::tag('span', $name, ['class' => 'c-btn-radio-item']); ?>
                                        <?php endforeach; ?>
                                        <?php foreach ($prefDistMasters as $name => $no): ?>
                                            <?= Html::tag('span', $name, ['class' => 'c-btn-radio-item']) ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="c-text-placeholder"><?= Yii::t('app', '条件なし') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="right">
                            <div class="s-btn-selet-change-block">
                                <?= Html::a(Yii::t('app', '{area}を変更', ['area' => $searchForm->searchKeys['pref']->searchkey_name]),
                                    '#ovl-area',
                                    ['class' => 'c-btn op-link op-select js-overlay-trigger-on']) ?>
                            </div>
                        </div>
                    </div>
                    <!--▲地域検索▲-->
                <?php endif; ?>
                <!--▼優先キー検索▼-->
                <?php if ($searchForm->principalKey): ?>
                    <div class="search-item-item-box">
                        <div class="left">
                            <div class="s-btn-radio-item-block">
                                <?php if ($principalParents || $principalChildren): ?>
                                    <div class="btn-radio-item js-omit-select-btn">
                                        <?php foreach ($principalParents as $name => $no): ?>
                                            <?= Html::tag('span', $name, ['class' => 'c-btn-radio-item']); ?>
                                        <?php endforeach; ?>
                                        <?php foreach ($principalChildren as $name => $no): ?>
                                            <?= Html::tag('span', $name, ['class' => 'c-btn-radio-item']) ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="c-text-placeholder"><?= Yii::t('app', '条件なし') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="right">
                            <div class="s-btn-selet-change-block">
                                <?= Html::a(Yii::t('app', '{key}を変更', ['key' => $principalKey->searchkey_name]),
                                    '#ovl-job',
                                    ['class' => 'c-btn op-link op-select js-overlay-trigger-on']) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!--▲優先キー検索▲-->
            </div>
            <!--▲地域or駅・優先キー検索▲-->
            <!--▼詳細検索▼-->
            <div class="s-job-result-selected-result-search-item-block">
                <div class="search-item-title">
                    <div class="c-title-notes"><?= Yii::t('app', '絞り込み条件') ?></div>
                </div>

                <div class="search-item-item-box">
                    <div class="left">
                        <div class="s-btn-radio-item-block">
                            <?php if ($conditions): ?>
                                <div class="btn-radio-item js-omit-select-btn">
                                    <?php foreach ($conditions as $attribute => $condition): ?>
                                        <?php foreach ($condition as $name => $no): ?>
                                            <?= Html::tag('span', $name, ['class' => 'c-btn-radio-item detail-item']); ?>
                                            <?= Html::hiddenInput($attribute . '[]', $no); ?>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="c-text-placeholder"><?= Yii::t('app', '条件なし') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="right">
                        <div class="s-btn-selet-change-block">
                            <?= Html::submitButton(
                                $conditions ? Yii::t('app', '変更') : Yii::t('app', '詳細条件の変更'),
                                ['class' => 'c-btn op-link op-select js-overlay-trigger-on']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--▲詳細検索▲-->
            <?= Html::hiddenInput('pref_string', implode(',', $prefs ?: []), ['id' => 'hidden-pref']); ?>
            <?= Html::hiddenInput('pref_dist_master_parent_string', implode(',', $prefDistMasterParents ?: []),
                ['id' => 'hidden-pref_dist_master_parent']); ?>
            <?= Html::hiddenInput('pref_dist_master_string', implode(',', $prefDistMasters ?: []), ['id' => 'hidden-pref_dist_master']); ?>

            <?= Html::hiddenInput('station_parent_string', implode(',', $stationParents ?: []), ['id' => 'hidden-station_parent']); ?>
            <?= Html::hiddenInput('station_string', implode(',', $stations ?: []), ['id' => 'hidden-station']); ?>

            <?= Html::hiddenInput('principal_parent_string', implode(',', $principalParents ?: []), ['id' => 'hidden-principal_parent']); ?>
            <?= Html::hiddenInput('principal_string', implode(',', $principalChildren ?: []), ['id' => 'hidden-principal']); ?>
            <?= Html::endForm(); ?>
        <?php endif; ?>

    </div>
</div>

<!-- 求人コンテンツ ===================================================-->
<?php
// todo このname_masterの仕様見直したい
$nameMaster = NameMaster::findOne(['name_id' => '2']);
$label      = $nameMaster->change_name ?: Yii::t('app', '求人');

$customFieldLayout = CustomField::customFieldHtml(Yii::$app->request->url);

$layout = '
<div class="result-paging_box clearfix">
    <div class="result-order-wrap">
        {sorter}<span class="fa fa-caret-down"></span>
    </div>
    <div class="result-num-wrap">
        {summary}
    </div>
</div>
    {items}
    <div class="result-paging_box clearfix">
        <div class="mod-pagination-wrap">{pager}</div>
    </div>';
?>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'layout'       => nl2br($customFieldLayout) . $layout,
    'emptyText'    => Yii::t('app', '条件に該当する{label}情報がありません。 検索条件を変えて、もう一度やり直してください。', ['label' => $label]),
    'itemView'     => '../_job_disp_itemview',
    'itemOptions'  => ['class' => 'mod-jobResultBox-wrap'],
    'summary'      => '<p class="result-num">' . Yii::t('app', '検索結果:') .
        '<span class="num-txt parentCount">{totalCount, number}</span>' .
        Yii::t('app', '件') .
        '</p>',
    'sorter'       => [
        'class'      => DropDownSorter::className(),
        'attributes' => ['updated_at', 'disp_start_date', 'disp_type_sort'],
        'options'    => [
            'class' => ['form-control mod-select1 result-order'],
        ],
    ],
    'pager'        => [
        'prevPageLabel'        => '<span class="fa fa-chevron-left"></span>',
        'nextPageLabel'        => '<span class="fa fa-chevron-right"></span>',
        'disabledPageCssClass' => 'last',
        'maxButtonCount'       => Yii::$app->request->get('page') >= 10 ? 3 : 5,
        'options'              => [
            'class' => 'mod-pagination',
        ],
    ],
]);
?>
<!-- ▼ここでコンテンツエンド▼-->
<!-- / .col-sm-12-->
<!-- 求人コンテンツ（旧バージョン利用 ===================================================//-->