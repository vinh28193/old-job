<?php

use app\models\manage\ManageMenuMain;
use proseeds\helpers\GridHelper;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

?>

    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

    <p class="alert alert-warning"><?= Yii::t('app', '求人原稿の掲載タイプの利用設定、名称設定を行います。') ?></p>
    <?= $message ?>

<?php $form = ActiveForm::begin(); ?>

    <!--一覧ボックス-->
<?= $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, [
    ['type' => 'number'],
    ['type' => 'default', 'attribute' => 'disp_type_name'],
    ['type' => 'default', 'attribute' => 'disp_type_no'],
    ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'isPublished'],
    ['type' => 'operation', 'buttons' => '{modalUpdate}']
],['renderCheckCount'=>false]) : '該当するデータがありません'
?>

<?php ActiveForm::end(); ?>

<?php
// updateのモーダルソースを表示
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'itemView' => '_update'
]);
?>