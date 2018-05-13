<?php
use app\common\csv\CsvDataProvider;
use app\models\manage\ManageMenuMain;
use proseeds\helpers\GridHelper;
use proseeds\widgets\grid\ProseedsGridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Alert;
use yii\widgets\Pjax;

/* @var CsvDataProvider $dataProvider */

$menu = ManageMenuMain::findFromRoute('manage/secure/settings/custom-field/list');
$this->title = Yii::t('app', 'カスタムフィールドCSV一括登録・変更');
$this->params['breadcrumbs'][] = ['label' => 'サイト設定', 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = ['label' => $menu->title, 'url' => ['secure/settings/custom-field/list']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="custom-field-index">

    <?= Html::tag('h1', Html::icon($menu->icon_key) . Html::encode($this->title), ['class' => 'heading']) ?>
    <div class="col-md-12" role="complementary">
        <?php
        echo Alert::widget([
            'body' => Html::tag('b', Yii::t('app', '以下の内容で登録してもよろしいですか？（まだ登録は完了していません。）')),
            'closeButton' => false,
            'options' => [
                'class' => 'alert alert-warning',
                'role' => 'alert',
            ],
        ]);
        echo Html::beginForm(Url::to(['register']), 'post', ['class' => 'form-inline']);
        echo Html::tag('p', implode(PHP_EOL, [
            Html::a(Yii::t('app', '戻る'), Url::to(['csv']), ['class' => 'btn btn-default']),
            Html::submitButton(Yii::t('app', '登録する'), ['class' => 'btn btn-lg btn-primary']),
        ]), ['align' => 'center', 'style' => Html::cssStyleFromArray(['margin-bottom' => '20px'])]);
        echo Html::hiddenInput('filename', $filename);
        echo Html::endForm();

        Pjax::begin();
        echo GridHelper::grid($dataProvider, [
            ['type' => 'default', 'attribute' => 'custom_no'],
            ['type' => 'default', 'attribute' => 'detail'],
            ['type' => 'default', 'attribute' => 'url'],
            ['type' => 'default', 'attribute' => 'pict'],
            ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'isPublished'],
        ], ['renderCheckCount' => false]);
        Pjax::end();
        ?>
    </div>

</div>

