<?php

use app\models\manage\ManageMenuMain;
use proseeds\widgets\PopoverWidget;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\manage\JobMaster */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('list'))->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="col-md-4">
        <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>
    </div>

<?= $this->render('form/_form', [
    'model' => $model,
]) ?>