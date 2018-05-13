<?php

use app\assets\MainAsset;
use app\common\Site;
use app\common\widget\DropDownSorter;
use app\models\forms\JobSearchForm;
use app\models\manage\CustomField;
use yii\web\View;
use yii\widgets\ListView;
use yii\bootstrap\BootstrapPluginAsset;
use app\models\manage\NameMaster;

BootstrapPluginAsset::register($this);

/* @var $this View */
/* @var $site Site */
/* @var $searchForm JobSearchForm */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $conditions array */
/* @var $breadcrumbs array パン屑のlinkに使う配列 */
/* @var $toolNo int tool_masterのno */
/* @var $searchNames int tool_masterのsearchName */
/* @var $conditionsCnt int 検索条件数 */

$this->registerJsFile('/js/module.js', [
    'depends' => MainAsset::className(),
]);

$this->params['overHeader'] = $this->render('_search-header', ['conditions' => $conditions, 'searchForm' => $searchForm]);
$this->params['bodyClass'] = 'type-pc';
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['keep'] = true;
?>

<?php
// todo このname_masterの仕様見直したい
$nameMaster = NameMaster::findOne(['name_id' => '2']);
$label = $nameMaster->change_name ?: Yii::t('app', '求人');

$h1 = Yii::t('app', '{label}検索結果', ['label' => $label]);

$resultTitle = '<h1 class="resultTitle">' . $site->toolMaster->h1 . '</h1>';

$customFieldLayout = CustomField::customFieldHtml(Yii::$app->request->url);

$layout = '
<div class="col-sm-12">
    <div class="result-paging_box clearfix">
        <div class="result-order-wrap">{sorter}<span class="fa fa-caret-down"></span></div>
        <div class="result-num-wrap">{summary}</div>
        <div class="mod-pagination-wrap hide-sp">{pager}</div>
    </div>
</div>
<div class="col-sm-12">
    {items}
    <div class="result-paging_box clearfix">
        <div class="mod-pagination-wrap">{pager}</div>
    </div>
</div>';

echo ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => $resultTitle . nl2br($customFieldLayout) . $layout,
    'emptyText' => Yii::t('app', '条件に該当する{label}情報がありません。 検索条件を変えて、もう一度やり直してください。', ['label' => $label]),
    'options' => ['class' => 'container subcontainer flexcontainer'],
    'itemView' => '_job_disp_itemview',
    'itemOptions' => ['class' => 'mod-jobResultBox-wrap'],

    'summary' => '<p class="result-num">' . Yii::t('app', '検索結果:') . '<span class="num-txt">{totalCount, number}</span>' . Yii::t('app', '件') . '</p>',

    'sorter' => [
        'class' => DropDownSorter::className(),
        'attributes' => ['updated_at', 'disp_start_date', 'disp_type_sort'],
        'options' => [
            'class' => ['form-control mod-select1 result-order'],
        ],
    ],

    'pager' => [
        'prevPageLabel' => '<span class="fa fa-chevron-left"></span>' . Yii::t('app', '前のページ'),
        'nextPageLabel' => '<span class="fa fa-chevron-right"></span>' . Yii::t('app', '次のページ'),
        'disabledPageCssClass' => 'last',
        'maxButtonCount' => Yii::$app->request->get('page') >= 10 ? 3 : 5,
        'options' => [
            'class' => 'mod-pagination',
        ],
    ],
]);
?>

