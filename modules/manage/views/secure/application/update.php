<?php

use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\manage\ApplicationMaster */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('list'))->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
$model->birth_date = Yii::$app->formatter->asDate($model->birth_date, 'short');
?>
<h1 class="heading">
    <?= Html::icon($menu->icon_key) . Html::encode($this->title) ?>
</h1>
<div class="container">
    <div class="row">
        <div class="col-md-12" role="complementary">
            <?= $this->render('_form', [
                'model' => $model
            ]) ?>
        </div>
    </div>
</div>