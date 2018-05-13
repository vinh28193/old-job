<?php

use proseeds\helpers\GridHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\models\manage\ManageMenuMain;
use proseeds\assets\BootBoxAsset;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $adminMasterSearch app\models\manage\adminMaster */
/* @var array $listItems */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

    <p class="alert alert-warning">
        GoogleAnalyticsからの集計をもとにページ毎にアクセス履歴を取得し、反映しています。
        応募完了数については、応募データから取得しています。<br />
        ※画面反映まで3~5分程、タイムラグがありますのでご了承ください。<br />
        ※先々月以前のデータは削除されます。必要な場合は、CSVダウンロードで保存してください。<br />
    </p>


<?= $this->render('@vendor/proseeds/proseeds/web/_deleteComment'); ?>

<?= $this->render('_search', ['accessLogSearch' => $accessLogSearch]); ?>

<?= $this->render('/secure/common/_buttons.php', [
    'pagename' => Yii::t('app', 'アクセス履歴'),
    'count' => $dataProvider->getTotalCount(),
    'buttons' => [
        'csv' => true,
    ],
]); ?>

<?php Pjax::begin();
if (Yii::$app->request->queryParams) {
    echo $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, $listItems, $config = ['id' => 'grid_id']) : Yii::t('app', '該当するデータがありません');
} else {
    echo Yii::t('app', '「この条件で表示する」ボタンを押せば一覧が表示されます');
}
Pjax::end(); ?>