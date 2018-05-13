<?php

use proseeds\assets\BootBoxAsset;
use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

BootBoxAsset::confirmBeforeSubmit($this, Yii::t('app', '削除したものは元に戻せません。削除しますか？'), '#grid_form');

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $jobMasterSearch app\models\manage\JobMasterSearch */
/* @var $corpList array */
/* @var $clientList array */
/* @var array $listItems */
/* @var $dataProvider yii\data\ActiveDataProvider */

$deleteMessage = Yii::t('app', '選択した求人原稿を削除します。よろしいですか？');
$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = $this->title;

// todo 公開／非公開ボタンのデザインCSS。今後共通に使用する場面が出てきたときに共通CSSへ移行を考える。
// なお、span.form-control-feedbackは審査モーダルに入力OKのチェックマークを出すのが見た目が悪かったので入れているCSS。
$css = <<<CSS
.valid-check {
    border-radius: 6px;
    color: white;
    cursor: pointer;
    display: block;
    padding: 5px 0px;
    overflow: hidden;
    text-align: center;
    text-overflow: ellipsis;
    white-space: nowrap;
}
span.form-control-feedback {
    display: none;
}
#valid-check-hint div {
    color: #34495e;
}
CSS;
$this->registerCss($css);
?>
    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

<?= $this->render('@vendor/proseeds/proseeds/web/_deleteComment'); ?>

<?= $this->render('_search', ['jobMasterSearch' => $jobMasterSearch,]); ?>

<?php
echo $this->render('/secure/common/_buttons.php', [
    'pagename' => Yii::t('app', '求人原稿'),
    'count' => $dataProvider->getTotalCount(),
    'buttons' => [
        'add' => true,
        'delete' => true,
        'csv' => true,
    ],
]);
Pjax::begin(['linkSelector' => '.pagination > li >  a']);
if (Yii::$app->request->queryParams) {
    echo $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, $listItems, $config = ['id' => 'grid_id']) : Yii::t('app', '該当するデータがありません');
} else {
    echo Yii::t('app', '「この条件で表示する」ボタンを押せば一覧が表示されます');
}
Pjax::end();
// 審査モーダル
// 審査機能がONの場合のみ
if (Yii::$app->tenant->tenant->review_use) {
    Pjax::begin([
        'id' => 'pjaxModal',
        'enablePushState' => false,
        'linkSelector' => '.pjaxModal',
    ]);
    Pjax::end();
}
