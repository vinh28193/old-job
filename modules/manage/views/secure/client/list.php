<?php

use proseeds\helpers\GridHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\models\manage\ManageMenuMain;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $clientMasterSearch app\models\manage\clientMasterSearch */
/* @var array $listItems */
/* @var $dataProvider yii\data\ActiveDataProvider */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

<?= $this->render('@vendor/proseeds/proseeds/web/_deleteComment'); ?>

<?= $this->render('_search', ['clientMasterSearch' => $clientMasterSearch]); ?>

<?= $this->render('/secure/common/_buttons.php', [
    'pagename' => Yii::t('app', '掲載企業'),
    'count' => $dataProvider->getTotalCount(),
    'buttons' => [
        'add' => true,
        'csv' => true,  // TODO:申し込み一覧CSVダウンロードが直ったらこのキーを「csvPlan」にする
        'delete' => true,
    ],
    'additionalDeleteConfirmComment' => Yii::t('app', '<br>掲載企業を削除すると関連の仕事情報も閲覧できなくなります。'),
]); ?>

<?php Pjax::begin();
if (Yii::$app->request->queryParams) {
    echo $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, $listItems, $config = ['id' => 'grid_id']) : Yii::t("app", '該当するデータがありません');
} else {
    echo Yii::t("app", '「この条件で表示する」ボタンを押せば一覧が表示されます');
}
Pjax::end(); ?>