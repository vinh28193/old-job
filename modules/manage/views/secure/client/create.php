<?php

use app\models\manage\ManageMenuMain;
use proseeds\widgets\PopoverWidget;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \app\models\manage\ClientMaster */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute('manage/secure/client/list')->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
//$this->registerJs('$(function(){$(".classification input[type=radio]").change(function(){'
//        . ''
//        . '});'
//        . '});', 
//        View::POS_END, 'my-options');
?>
<h1 class="heading">
    <?= Html::icon($menu->icon_key) . Html::encode($this->title) ?>
    <?= PopoverWidget::widget([
        'dataContent' => Yii::t('app', '掲載企業情報を入力してください。'),
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
