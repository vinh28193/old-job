<?php

use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use proseeds\assets\BootBoxAsset;
use yii\widgets\Pjax;

BootBoxAsset::confirmBeforeSubmit($this, Yii::t('app', '削除したものは元に戻せません。削除しますか？'), '#grid_form');

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model app\modules\manage\models\search\FreeContentSearch */
/* @var array $listItems */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->name;
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

<?= $this->render('@vendor/proseeds/proseeds/web/_deleteComment'); ?>

<?= $this->render('_search', ['model' => $model]); ?>

<?= $this->render('/secure/common/_buttons.php', [
    'pagename' => $this->title,
    'count' => $dataProvider->getTotalCount(),
    'buttons' => [
        'add' => true,
        'delete' => true,
    ],
]); ?>

<?php Pjax::begin(['linkSelector' => '.pagination > li >  a']);
if (Yii::$app->request->queryParams) {
    echo $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, $listItems, ['id' => 'grid_id']) : Yii::t('app', '該当するデータがありません');
} else {
    echo Yii::t('app', '「この条件で表示する」ボタンを押せば一覧が表示されます');
}
Pjax::end();
