<?php

use proseeds\helpers\GridHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Alert;
use yii\widgets\Pjax;
use app\models\manage\ManageMenuMain;

/* @var \app\common\csv\CsvDataProvider $dataProvider */
/* @var array $listItems */
/* @var array $filename */

// todo ぱんくずのURLリンク用に、暫定的にベタ打ちにしているので、ベタ打ちにしなくてもよいようにする
$menu = ManageMenuMain::findFromRoute('manage/secure/job-csv/index');
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('secure/job/list'))->title, 'url' => ['secure/job/list']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= \proseeds\helpers\CommonHtml::manageTitle($this->title) ?>

<div class="container">
    <div class="row">
        <div class="col-md-12" role="complementary">
            <?php
            echo Alert::widget([
                'body' => Html::tag("span", "", ["class" => "glyphicon glyphicon-warning-sign"])
                    . Html::tag("b", Yii::t("app", "以下の内容で登録してもよろしいですか？（まだ登録は完了していません。）")),
                'closeButton' => false,
                'options' => [
                    'class' => "alert alert-warning",
                    'role' => 'alert',
                ],
            ]);
            echo Html::beginForm(Url::to(['register']), 'post', ['class' => 'form-inline']);
            echo Html::tag('p', implode(PHP_EOL, [
                Html::a(Yii::t("app", "戻る"), Url::to(["index"]), ['class' => 'btn btn-default']),
                Html::submitButton(Yii::t("app", "登録する"), ['class' => 'btn btn-lg btn-primary']),
            ]), ['align' => 'center', 'style' => Html::cssStyleFromArray(['margin-bottom' => '20px'])]);
            echo Html::hiddenInput("filename", $filename);
            echo Html::endForm();

            Pjax::begin();
            echo GridHelper::grid($dataProvider, $listItems, $config = ['id' => 'grid_id', 'renderCheckCount' => false]);
            Pjax::end();
            ?>
        </div>
    </div>
</div>