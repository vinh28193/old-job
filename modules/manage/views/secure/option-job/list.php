<?php

use app\models\manage\BaseColumnSet;
use app\models\manage\JobColumnSet;
use app\models\manage\JobColumnSubset;
use app\models\manage\ManageMenuMain;
use proseeds\helpers\GridHelper;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $nameArray array */
/* @var $searchModel \app\models\manage\JobColumnSetSearch */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

$subsetModel = new JobColumnSubset();
$model = new JobColumnSet();

$this->render('/secure/common/_subset-js.php', [
    'model' => $model,
    'subsetModel' => $subsetModel,
]);
?>

    <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>

    <p class="alert alert-warning">
        <?= Yii::t('app', '求人原稿情報を登録する際の項目設定を変更します。<br />
<br />
FW対象：求職者画面からフリーワードで検索できる項目になります。<br />
入力条件：必須入力/任意入力を設定します。ただし、必須（固定）の項目は変更することができません。<br />
検索一覧表示：「表示」に設定すると管理画面のメニュー[求人原稿]-[求人情報一覧]で検索を行った際に検索結果一覧に表示されます。<br />
検索項目表示：「表示」に設定すると管理画面のメニュー[求人原稿]-[求人情報一覧]で検索条件として選択できるようになります。<br />
公開状況：求人原稿の登録情報として使用する項目かどうかを設定します。「非公開」に設定した場合、上記２条件の設定が「表示」であっても各画面上に表示されません。') ?><br>
        <?= Yii::t('app', '求人原稿の表示項目を変更されたい方は<a href="/manage/secure/settings/display/index">こちら</a>。') ?>
    </p>

<?= Yii::$app->session->getFlash('updateComment') ?>

    <!--検索ボックス-->
<?= $this->render('/secure/common/_option-search.php', ['searchModel' => $searchModel]); ?>

    <!--一覧ボックス-->
<?= $dataProvider->count ? GridHelper::grid($dataProvider, [
    ['type' => 'number'],
    ['type' => 'default', 'attribute' => 'label', 'format' => 'html', 'usePlainValueInPop' => 'true', 'headerClass' => 'm-column',
        'value' => function ($model, $key, $index, $column) {
            if ($model->freeword_search_flg) {
                $itemName = Html::tag(
                        'span',
                        Yii::t('app', 'FW対象'),
                        [
                            'class' => 'label label-info label-fix-80',
                            'style' => 'margin-right:5px;',
                            'title' => '',
                            'data-placement' => 'top-right:5px;',
                            'data-toggle' => 'tooltip',
                            'data-original-title' => Yii::t('app', 'フリーワード検索対象項目です。'),
                        ]
                    ) . $model->label;
            } else {
                $itemName = Html::tag(
                        'span',
                        Yii::t('app', 'FW対象外'),
                        [
                            'class' => 'label label-default label-fix-80',
                            'style' => 'margin-right:5px;',
                            'title' => '',
                            'data-placement' => 'top-right:5px;',
                            'data-toggle' => 'tooltip',
                            'data-original-title' => Yii::t('app', 'フリーワード検索対象外項目です。'),
                        ]
                    ) . $model->label;
            }
            return $itemName;
        }
    ],
    ['type' => 'default', 'attribute' => 'is_must', 'format' => 'isMustItem', 'header'],
    ['type' => 'default', 'attribute' => 'data_type', 'value' => function ($model, $key, $index, $column) {
        if ($model->data_type  == BaseColumnSet::DATA_TYPE_RADIO) {
            return 'プルダウン';
        } else {
            return $model->data_type;
        }
    }],
    ['type' => 'default', 'attribute' => 'is_in_list', 'format' => 'isSearchMenuItem'],
    ['type' => 'default', 'attribute' => 'is_in_search', 'format' => 'isSearchMenuItem'],
    ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'isUsed'],
    ['type' => 'operation', 'buttons' => '{pjax-modal}'],
], ['renderCheckCount' => false]) : Yii::t('app', '該当するデータがありません');

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);
Pjax::end();