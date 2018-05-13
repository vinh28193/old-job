<?php
use app\models\manage\CustomField;
use app\models\manage\WidgetData;
use kartik\widgets\FileInput;
use proseeds\widgets\TableForm;
use yii\widgets\Pjax;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use app\models\manage\CustomFieldSearch;

/* @var CustomFieldSearch $searchModel */
/* @var ActiveDataProvider $dataProvider */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title ?? Yii::t('app', 'カスタムフィールド設定');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

// ファイルインプットのデザイン
$css = <<<CSS
.kv-preview-thumb div.kv-file-content {
    height: auto !important;
}
.kv-preview-thumb div.kv-file-content img{
    height: auto !important;
    max-width: 200px;
    max-height: 160px;
}
CSS;
$this->registerCss($css);
?>

<div class="custom-field-list">

    <?php echo Html::tag('h1', Html::icon($menu->icon_key) . Yii::t('app', Html::encode($this->title)), ['class' => 'heading']) ?>
    <?php echo Html::tag('p', Yii::t('app', '求職者やクローラーが求職者画面の検索結果ページで何のページか、より認識しやすくするため、<br />
指定したURL（検索結果ページのみ）に、自由に画像やメッセージを表示する事ができます。<br />
<br />
1件ずつ登録する場合は、「カスタムフィールドを登録する」をクリックしてください。<br />
一括登録する場合は、「CSV一括登録・変更する」をクリックしてください。<br />
※一括変更する場合は、変更したいデータをCSVダウンロードしてから、「CSV一括登録・変更する」をクリックしてください。'), ['class' => 'alert alert-warning']); ?>
    <?php echo Yii::$app->session->getFlash('resultComment'); ?>
    <?php echo $this->render('_search', ['searchModel' => $searchModel]); ?>
    <?php echo $this->render('/secure/common/_buttons.php', [
        'pagename' => Yii::t('app', 'カスタムフィールド'),
        'count' => $dataProvider->getTotalCount(),
        'buttons' => [
            'addModal' => true,
            'csvEdit' => true,
            'delete' => true,
            'csv' => true,
        ],
    ]); ?>

    <?php
    if (Yii::$app->request->queryParams) {
        echo $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, [
            ['type' => 'checkBox'],
            ['type' => 'default', 'attribute' => 'custom_no'],
            ['type' => 'default', 'attribute' => 'detail'],
            ['type' => 'default', 'attribute' => 'url'],
            ['type' => 'default', 'attribute' => 'pict'],
            ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'isPublished' ],
            ['type' => 'operation', 'buttons' => '{pjax-modal}'],
        ], ['renderCheckCount' => true, 'id' => 'grid_id']) : Yii::t('app', '該当するデータがありません');
    } else {
        echo Yii::t("app", '「この条件で表示する」ボタンを押せば一覧が表示されます');
    }
    ?>

    <?php
        Pjax::begin([
            'id' => 'pjaxModal',
            'enablePushState' => false,
            'linkSelector' => '.pjaxModal',
        ]);
        // pjax遷移先で使うjsのcss生成のために仮置き
        $tableForm = TableForm::begin([
            'id' => 'form',
            'options' => ['enctype' => 'multipart/form-data'],
            'tableOptions' => ['class' => 'table table-bordered'],
        ]);
        /** @var CustomField $model */
        $model = new CustomField();
        $tableForm->form($model, 'pict')->widget(FileInput::className(), [
            'pluginOptions' => [
                'showCaption' => false,
                'showPreview' => false,
                'showRemove' => false,
                'showUpload' => false,
                'showCancel' => false,
                'showClose' => false,
                'showUploadedThumbs' => false,
                'browseClass' => 'hidden',
                'layoutTemplates' => ['footer' => '', 'actions' => '',],
            ]
        ]);
        $tableForm->row($model, 'valid_chk')->radioList(CustomField::validChkLabel());
        TableForm::end();
        Pjax::end();
    ?>

</div>
