<?php

use app\models\manage\ManageMenuMain;
use proseeds\helpers\GridHelper;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\manage\BaseColumnSet */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

?>

    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

    <p class="alert alert-warning"><?= Yii::t('app', '管理者情報を登録する際の項目設定を変更します。<br />
<br />
入力条件：必須入力/任意入力を設定します。ただし、必須（固定）の項目は変更することができません。<br />
検索一覧表示：「表示」に設定すると管理画面のメニュー[管理者]-[管理者情報一覧]で検索を行った際に検索結果一覧に表示されます。<br />
検索項目表示：「表示」に設定すると管理画面のメニュー[管理者]-[管理者情報一覧]で検索条件として選択できるようになります。<br />
公開状況：運営元・代理店・掲載企業の担当者情報として使用する項目かどうかを設定します。「非公開」に設定した場合、上記２条件の設定が「表示」であっても画面上に表示されません。') ?></p>

<?= Yii::$app->session->getFlash('updateComment') ?>

    <!--検索ボックス-->
<?= $this->render('/secure/common/_option-search.php', ['searchModel' => $searchModel]); ?>

    <!--一覧ボックス-->
<?= $dataProvider->count ? GridHelper::grid($dataProvider, [
    ['type' => 'number'],
    ['type' => 'default', 'attribute' => 'label', 'headerClass' => 'm-column'],
    ['type' => 'default', 'attribute' => 'is_must', 'format' => 'isMustItem'],
    ['type' => 'default', 'attribute' => 'data_type'],
    ['type' => 'default', 'attribute' => 'is_in_list', 'format' => 'isListMenuItem'],
    ['type' => 'default', 'attribute' => 'is_in_search', 'format' => 'isSearchMenuItem'],
    ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'isPublished'],
    ['type' => 'operation', 'buttons' => '{pjax-modal}'],
], ['renderCheckCount' => false]) : Yii::t('app', '該当するデータがありません');

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);
Pjax::end();