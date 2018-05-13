<?php

use app\models\manage\ClientColumnSet;
use app\models\manage\DispType;
use app\models\manage\JobColumnSet;
use app\models\manage\MainDisplay;
use app\models\manage\ManageMenuMain;
use kartik\form\ActiveForm;
use proseeds\assets\BootBoxAsset;
use proseeds\widgets\PopoverWidget;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\common\PostablePjax;
use app\modules\manage\controllers\secure\settings\DisplayController;

/* @var $this yii\web\View */
/* @var $pjaxId string */
/* @var $dispTypeId integer */
/* @var $dispTypes DispType[] */
/* @var $bothListItems JobColumnSet[] */
/* @var $bothClientItems ClientColumnSet[] */
/* @var $mainDisplayModel mainDisplay */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;
BootBoxAsset::confirmBeforeSubmit($this, Yii::t('app', '求人メイン・リスト項目を変更してもよろしいですか？'));
if (Yii::$app->session->hasFlash('dispTypeId')) {
    $dispTypeId = Yii::$app->session->getFlash('dispTypeId');
} else {
    $dispTypeId = $dispTypes[0]->id;
}

$css = <<<CSS
.sort-wrapper ul.sortable {
  background-color: #f5f5f5;
}
li.ui-sortable-handle {
  background-color: #ffffff;
}
CSS;
$this->registerCss($css);

?>

<?= Html::tag('h1', Html::icon($menu->icon_key) . Html::encode($this->title), ['class' => 'heading']); ?>
<div class="row">
    <?php
    $form = ActiveForm::begin([
        'id' => 'form',
        'action' => 'update',
    ]);
    echo Html::hiddenInput('dispTypeId', $dispTypeId);

    ?>
    <div class="col-md-10 col-md-offset-1" role="complementary">
        <p class="alert alert-warning">
            <?= Yii::t('app', '求人詳細画面の下部に表示される求人メイン・リスト項目のレイアウト設定をします。設定しない項目は、求人詳細画面には表示されません。') ?><br>
            <?= Yii::t('app', '表示させたい項目は、右の項目欄より左のレイアウト表示にドラッグしてください。非表示にしたい場合は、右の項目欄にドラッグして戻してください。') ?><br>
            <?= Yii::t('app', '※「求人原稿項目設定」を変更されたい方は<a href="/manage/secure/option-job/list">こちら</a>。') ?><br>
            <?= Yii::t('app', '※「掲載企業項目設定」を変更されたい方は<a href="/manage/secure/option-client/list">こちら</a>。') ?><br>
        </p>
        <?= Yii::$app->session->getFlash('operationComment') ?>
        <?= Html::dropDownList('dispTypeId', null, ArrayHelper::map($dispTypes, 'id', 'disp_type_name'), [
            'id' => 'dispTypeId',
            'class' => 'form-control jq-placeholder',
            'options' => [
                $dispTypeId => ['selected' => 'selected'],
            ],
        ]); ?>
        <div style="margin-top: 15px;">
            <?= PopoverWidget::widget([
                'content' => Html::tag('span', Yii::t('app', '画像イメージ(求人メイン項目)'), ['class' => 'btn btn-danger btn-sm']),
                'dataContent' => '<img src="/pict/display_main.png" style="width: 100%">',
                'dataHtml' => true,
            ]); ?>
            <?= PopoverWidget::widget([
                'content' => Html::tag('span', Yii::t('app', '画像イメージ(求人リスト項目)'), ['class' => 'btn btn-danger btn-sm']),
                'dataContent' => '<img src="/pict/display_list_client.png" style="width: 100%">',
                'dataHtml' => true,
            ]); ?>
        </div>
    </div>
    <?php
    PostablePjax::begin([
        'id' => DisplayController::PJAX_ID,
        'enablePushState' => false,
        'url' => Url::toRoute('pjax-form'),
        'trigger' => ['selector' => '"#dispTypeId"', 'event' => 'change'],
        'postAttribute' => 'value',
        'postAttributeName' => 'dispTypeId',
        'options' => [
            'class' => 'col-md-10 col-md-offset-1',
            'style' => 'margin-top: 20px',
        ],
    ]);

    echo $this->render('_input-fields', [
        'dispTypeId' => $dispTypeId,
        'bothListItems' => $bothListItems,
        'bothClientItems' => $bothClientItems,
        'mainDisplayModel' => $mainDisplayModel,
        'form' => $form,
    ]);

    PostablePjax::end();
    ActiveForm::end();
    ?>
</div>
