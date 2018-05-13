<?php

use app\models\manage\SendMailSet;
use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use proseeds\assets\BootBoxAsset;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\SendMailSetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

BootBoxAsset::confirmBeforeSubmit($this, Yii::t('app', '削除したものは元に戻せません。削除しますか？'), '#grid_form');
$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="send-mail-set-index">

    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['searchModel' => $searchModel]); ?>

    <?= $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, [
        ['type' => 'number'],
        ['type' => 'default', 'attribute' => 'mail_name'],
        ['type' => 'default', 'attribute' => 'mail_to', 'format' => 'isMailTo'],
        ['type' => 'default', 'attribute' => 'mail_to_description'],
        ['type' => 'default', 'attribute' => 'from_name'],
        ['type' => 'default', 'attribute' => 'from_address'],
        ['type' => 'default', 'attribute' => 'subject'],
        ['type' => 'default', 'attribute' => 'contents'],
        ['type' => 'default', 'attribute' => 'mail_sign'],
        ['type' => 'operation', 'buttons' => '{pjax-modal}'],
    ], ['renderCheckCount' => false]) : Yii::t('app', '該当するデータがありません') ?>

    <?php
    Pjax::begin([
        'id' => 'pjaxModal',
        'enablePushState' => false,
        'linkSelector' => '.pjaxModal',
    ]);
    if ($mailTypeId = ArrayHelper::getValue(Yii::$app->request->queryParams, 'mailTypeId')) {
        echo $this->render('_item-update', ['model' => SendMailSet::findOne(['mail_type_id' => $mailTypeId])]);
    }

    Pjax::end();
    ?>

</div>