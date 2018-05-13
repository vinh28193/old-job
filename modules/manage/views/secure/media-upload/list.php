<?php

use app\modules\manage\models\Manager;
use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\widgets\Pjax;
use app\models\manage\MediaUploadSearch;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $mediaUploadSearch app\models\manage\MediaUpload */
/* @var $listItems array */
/* @var $isOnlyView bool */
/** @var Manager $identity */
$identity = Yii::$app->user->identity;
$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = $this->title;
?>

    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>
<?php //TODO:メッセージ表示、全画面共通化 ?>
<?= Yii::$app->session->getFlash('errorMessage') ? '<p class="alert alert-danger">' .
    Yii::$app->session->getFlash('errorMessage') . '</p>' : '' ?>

<?= Yii::$app->session->getFlash('message') ? '<p class="alert alert-success">' .
    Yii::$app->session->getFlash('message') . '</p>' : '' ?>
    <p class="alert alert-warning">
        <?= Yii::t('app', 'アップロードした画像は掲載企業が原稿を登録する際にサンプル画像として表示されます。<br>') ?>
        <?php
        $sortSize = Yii::$app->formatter->asShortSize(MediaUploadSearch::getTotalFileSize());
        if ($identity->myRole == Manager::CLIENT_ADMIN) {
            echo Yii::t("app", '貴社の画像の合計サイズは{totalSize}です', [
                'totalSize' => $sortSize,
            ]);
        } else {
            echo Yii::t("app", '画像の合計サイズは{totalSize}です', [
                'totalSize' => $sortSize,
            ]);
        }
         ?>
    </p>


<?= $this->render('@vendor/proseeds/proseeds/web/_deleteComment'); ?>

<?= $this->render('_search', ['mediaUploadSearch' => $mediaUploadSearch]); ?>

<?php
if ($isOnlyView) {
    $buttonArray = [
        'add' => true
    ];
} else {
    $buttonArray = [
        'delete' => !$isOnlyView,
        'add' => true
    ];
}

echo $this->render('/secure/common/_buttons.php', [
    'pagename' => Yii::t('app', '画像'),
    'count' => $dataProvider->getTotalCount(),
    'buttons' => $buttonArray,
    'additionalDeleteConfirmComment' => Yii::t('app',
        '<br>画像を削除すると使用している仕事情報の画像も閲覧できなくなります。'),
]); ?>

<?php
if (Yii::$app->request->queryParams) {
    if ($dataProvider->getTotalCount()) {
        echo GridHelper::grid($dataProvider, $listItems, $config = ['id' => 'grid_id']);
        Pjax::begin([
            'id' => 'pjaxModal',
            'enablePushState' => false,
            'linkSelector' => '.pjaxModal',
            'clientOptions' => [
                'data' => Yii::$app->request->queryParams,
            ],
        ]);
        Pjax::end();
    } else {
        echo Yii::t("app", '該当するデータがありません');
    }
} else {
    echo Yii::t("app", '「この条件で表示する」ボタンを押せば一覧が表示されます');
} ?>