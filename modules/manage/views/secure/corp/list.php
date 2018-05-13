<?php

use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use proseeds\assets\BootBoxAsset;
use yii\widgets\Pjax;

BootBoxAsset::confirmBeforeSubmit($this, Yii::t('app', '削除したものは元に戻せません。削除しますか？'), '#grid_form');

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $corpMasterSearch app\models\manage\CorpMaster */
/* @var array $listItems */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

<?= $this->render('@vendor/proseeds/proseeds/web/_deleteComment'); ?>

<?= $this->render('_search', ['corpMasterSearch' => $corpMasterSearch]); ?>

<?= $this->render('/secure/common/_buttons.php', [
    'pagename' => Yii::t('app', '代理店'),
    'count' => $dataProvider->getTotalCount(),
    'buttons' => [
        'add' => true,
        'csv' => true,
        'delete' => true,
    ],
    'additionalDeleteConfirmComment' => Yii::t('app', '<br>代理店を削除すると関連の仕事情報も閲覧できなくなります。'),
]); ?>

<?php Pjax::begin();
if (Yii::$app->request->queryParams) {
    echo $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, $listItems, $config = ['id' => 'grid_id']) : Yii::t("app", '該当するデータがありません');
} else {
    echo Yii::t("app", '「この条件で表示する」ボタンを押せば一覧が表示されます');
}
Pjax::end(); ?>