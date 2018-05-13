<?php

use proseeds\widgets\PopoverWidget;
use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\manage\CorpMaster */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('list'))->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 class="heading">
    <?= Html::icon($menu->icon_key) . Html::encode($this->title) ?>
    <?= PopoverWidget::widget([
        'dataContent' => Yii::t('app', '代理店情報を入力してください。'),
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