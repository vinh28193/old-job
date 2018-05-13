<?php

use app\models\manage\SearchkeyMaster;
use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use proseeds\widgets\PopoverWidget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\SearchkeyMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="send-mail-set-index">

    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>
    <p class="alert alert-warning">
        <?= Yii::t('app', '色付きのキーは優先キーです。') ?><br />
        <?= Yii::t('app', '優先キーは以下のような特殊な動きをする検索キーです') ?><br />
        <?= Yii::t('app', '・スマートフォン版にて都道府県と共にトップからも検索できるようになります') ?><br />
        <?= Yii::t('app', '・スマートフォン版にて勤務地もしくは路線駅と共に検索結果画面からも検索できるようになります') ?><br />
        <?= Yii::t('app', '・スマートフォン版にて検索UIがリッチなものになります') ?><br />
        <?= Yii::t('app', '・スマートフォン版及びPC版にて、検索フォームの上部に表示されます') ?>
    </p>

    <?= Yii::$app->session->getFlash('updateComment') ?>

    <?php echo $this->render('_search', ['searchModel' => $searchModel]); ?>
    <?= $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider,
        [
            ['type' => 'default', 'attribute' => 'searchkey_name'],
            ['type' => 'default', 'attribute' => 'sort', 'headerClass' => 'ss-column'],
            ['type' => 'default', 'attribute' => 'is_on_top', 'format' => 'isOnTop'],
            ['type' => 'default', 'attribute' => 'is_and_search', 'format' => 'isAndSearch'],
            ['type' => 'default', 'attribute' => 'searchInputToolGrid'],
            ['type' => 'default', 'attribute' => 'isLabelForGrid'],
            ['type' => 'default', 'attribute' => 'icon_flg', 'format' => 'isIconFlg'],
            ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'validChk'],
            ['type' => 'operation', 'buttons' => '{pjax-modal}'],
        ],
        [
            'renderCheckCount' => false,
            'rowOptions' => function ($model, $key, $index, $grid) {
                if ($model->principal_flg == 1) {
                    return ['style' => 'background-color:#FFCC00;'];
                } else {
                    return [];
                }
            },
        ]
    ) : Yii::t('app', '該当するデータがありません') ?>

    <?php
    Pjax::begin([
        'id' => 'pjaxModal',
        'enablePushState' => false,
        'linkSelector' => '.pjaxModal',
    ]);
    if ($id = ArrayHelper::getValue(Yii::$app->request->queryParams, 'id')) {
        echo $this->render('_item-update', ['model' => SearchkeyMaster::findOne(['id' => $id])]);
    }

    Pjax::end();
    ?>
</div>
