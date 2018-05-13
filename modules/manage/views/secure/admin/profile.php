<?php

use yii\helpers\Html;
use app\models\manage\ManageMenuMain;
use proseeds\widgets\PopoverWidget;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\manage\CorpMaster */

$isProfile = !isset(ManageMenuMain::findFromRoute(Yii::$app->requestedRoute)->title) ? true : false;
//ToDoマイプロフィールタイトルがないため暫定的な処理、タイトルが入るようになれば条件をはずします。
$this->title = $isProfile ? Yii::t('app', 'マイプロフィール編集') : ManageMenuMain::findFromRoute(Yii::$app->requestedRoute)->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('list'))->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1 class="heading">
    <span class="glyphicon glyphicon-list-alt"></span>
    <?= Html::encode($this->title) ?>
    <?= PopoverWidget::widget([
        'dataContent' => $isProfile ? Yii::t('app', 'マイプロフィールを入力してください。') : Yii::t('app', '管理者情報を入力してください。'),
    ]); ?>
</h1>
<div class="container">
    <div class="row">
        <div class="col-md-10" role="complementary">
            <?= $this->render('_profile-form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>