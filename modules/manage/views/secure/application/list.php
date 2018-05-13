<?php

use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $applicationMasterSearch app\models\manage\ApplicationMaster */
/* @var array $listItems */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

<?= $this->render('@vendor/proseeds/proseeds/web/_deleteComment'); ?>

<?= $this->render('_search', ['applicationMasterSearch' => $applicationMasterSearch]); ?>

<?= $this->render('/secure/common/_buttons.php', [
    'pagename' => Yii::t('app', '応募者'),
    'count' => $dataProvider->getTotalCount(),
    'buttons' => [
        'delete' => true,
        'csv' => true,
    ],
]); ?>

<?php Pjax::begin();
if (Yii::$app->request->queryParams) {
    echo $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, $listItems, $config = ['id' => 'grid_id']) : Yii::t("app", '該当するデータがありません');
} else {
    echo Yii::t("app", '「この条件で表示する」ボタンを押せば一覧が表示されます');
}
Pjax::end(); ?>