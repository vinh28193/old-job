<?php

use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\helpers\Url;
use proseeds\assets\BootBoxAsset;

/* @var $this yii\web\View */
/* @var $model app\models\manage\Policy */

$this->title = ManageMenuMain::findFromRoute(Url::toRoute('list'))->title .Yii::t('app','の編集');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('list'))->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;

BootBoxAsset::confirmBeforeSubmit($this, $model->isNewRecord ? Yii::t("app", "規約情報を登録してもよろしいですか？") : Yii::t("app", "規約情報を変更してもよろしいですか？"));

?>
<div class="policy-update">

    <h1><?=Html::icon('wrench') . Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
