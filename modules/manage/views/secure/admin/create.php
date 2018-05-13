<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use app\models\manage\ManageMenuMain;
use proseeds\widgets\PopoverWidget;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \app\models\manage\AdminMaster */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('list'))->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 class="heading">
    <?= Html::icon($menu->icon_key) . Html::encode($this->title) ?>
    <?= PopoverWidget::widget([
        'dataContent' => Yii::t('app', '管理者情報を入力してください。'),
    ]); ?>
</h1>
<div class="container">
    <div class="row">
        <div class="col-md-10" role="complementary">
            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>
