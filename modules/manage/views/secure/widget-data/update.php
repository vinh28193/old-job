<?php

use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model app\models\manage\WidgetData */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ウィジェットデータ一覧・編集'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<h1 class="heading">
    <?= Html::icon($menu->icon_key) . Html::encode($this->title) ?>
</h1>
<div class="container">
    <div class="row">
        <div class="col-md-10" role="complementary">
            <?php
            echo $model->widget->widget_name;
            echo $this->render('_input-fields', ['model' => $model]);
            ?>
        </div>
    </div>
</div>
