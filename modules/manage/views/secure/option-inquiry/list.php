<?php

use app\models\manage\InquiryColumnSubset;
use app\models\manage\InquiryColumnSet;
use app\models\manage\ManageMenuMain;
use proseeds\helpers\GridHelper;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\manage\FunctionItemSetSearch */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

$subsetModel = new InquiryColumnSubset();
$model = new InquiryColumnSet();

$this->render('/secure/common/_subset-js.php', [
    'model' => $model,
    'subsetModel' => $subsetModel,
]);
?>
    <?=Html::beginTag('h1',['class'=>'heading']) ?>
    <?=Html::icon($menu->icon_key) . Html::encode($this->title) ?>
    <?=Html::endTag('h1')?>

    <?=Html::beginTag('p',['class'=>'alert alert-warning']) ?>
    <?= Yii::t('app', '掲載の問いあわせ情報を登録する際の項目設定を変更します。<br />
    <br />
    入力条件：必須入力/任意入力を設定します。ただし、必須（固定）の項目は変更することができません。<br />
    公開状況：掲載の問いあわせの入力情報として使用する項目かどうかを設定します。') ?>
    <?=Html::endTag('h1')?>

<?= Yii::$app->session->getFlash('updateComment') ?>

    <!--検索ボックス-->
<?= $this->render('/secure/common/_option-search.php', ['searchModel' => $searchModel]); ?>

    <!--一覧ボックス-->
<?= $dataProvider->count ? GridHelper::grid($dataProvider, [
    ['type' => 'number'],
    ['type' => 'default', 'attribute' => 'label', 'headerClass' => 'm-column'],
    ['type' => 'default', 'attribute' => 'is_must', 'format' => 'isMustItem'],
    ['type' => 'default', 'attribute' => 'data_type'],
    ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'isPublished'],
    ['type' => 'operation', 'buttons' => '{pjax-modal}'],
], ['renderCheckCount' => false]) : Yii::t('app', '該当するデータがありません');

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);
Pjax::end();