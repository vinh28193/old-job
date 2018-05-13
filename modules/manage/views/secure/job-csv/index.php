<?php

use kartik\file\FileInput;
use yii\helpers\Url;
use yii\bootstrap\Html;
use app\models\manage\ManageMenuMain;
use app\modules\manage\controllers\secure\CsvHelperController;

$menu = ManageMenuMain::findFromRoute('manage/secure/job-csv/index');
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('secure/job/list'))->title, 'url' => ['secure/job/list']];
$this->params['breadcrumbs'][] = $this->title;
$clientChargePlanLabel = Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label;

//ファイルのアップロードに成功したら、verifyアクションにリダイレクト
$redirectUrl = Url::to(['verify']);
$redirectJs = <<<JS
$("#csv-uploader").on('fileselect', function(event, data) {
    $(this).fileinput('upload').fileinput('disable');
});
$("#csv-uploader").on('fileuploaded', function(event, data) {
    window.location.href = "{$redirectUrl}" + "?filename=" + data.response.filename;
});
JS;
$this->registerJs($redirectJs);
?>

<?= \proseeds\helpers\CommonHtml::manageTitle(Yii::t('app', $this->title), '', $menu->icon_key) ?>

<div class="container">
    <div class="row">
        <div class="col-md-10" role="complementary">
            <?php
            if (Yii::$app->session->hasFlash("csvError")) {
                foreach ((array)Yii::$app->session->getFlash("csvError") as $error) {
                    echo \yii\bootstrap\Alert::widget(['body' => $error, 'options' => ['class' => 'alert-danger']]);
                }
            }

            $content = Yii::t("app", "<b>登録手順</b>：");
            $content .= Html::beginTag("ul");
            $content .= Html::tag("li", Yii::t("app", "「CSVテンプレートをダウンロード」をクリックし、CSVテンプレートをダウンロードします"));
            $content .= Html::tag("li", Yii::t("app", "「CSVの入力方法」に従って、CSVテンプレートに登録する求人原稿の情報を書き込みます"));
            $content .= Html::tag("li", Yii::t("app", "書き込んだCSVをこの画面からアップロードします"));
            $content .= Html::endTag("ul");

            $content .= Html::beginTag("div", ['style' => Html::cssStyleFromArray(['margin' => '10px 0px'])]);
            $buttons = [
                Html::a(Html::icon("download-alt") . Yii::t("app", "CSVテンプレートをダウンロード"), Url::to(["csv-download"]), ['class' => 'btn btn-info']),
                Html::a(Html::icon("question-sign") . Yii::t("app", "CSVの入力方法"), "#", ['class' => 'btn btn-simple btn-sm', 'onclick' => "javascript:window.open('" . Url::to(['help']) . "', 'help', 'width=700,height=800')"]),
            ];
            foreach ($buttons as $button) {
                $content .= Html::tag("span", $button, ['style' => Html::cssStyleFromArray(['margin' => "0px 5px"])]);
            }
            $content .= Html::endTag("div");

            $content .= Yii::t("app", "<b>変更手順</b>：");
            $content .= Html::beginTag("ul");
            $content .= Html::tag("li", Yii::t("app", "「求人情報の管理」から、変更したい求人原稿を選択して、「CSVダウンロード」をクリックします"));
            $content .= Html::tag("li", Yii::t("app", "「CSVの入力方法」に従って、ダウンロードされたCSVを編集します"));
            $content .= Html::tag("li", Yii::t("app", "編集したCSVをこの画面からアップロードします"));
            $content .= Html::endTag("ul");

            $content .= Html::beginTag("div", ['style' => Html::cssStyleFromArray(['margin' => '10px 0px'])]);
            $button = Html::a(Html::icon("list") . Yii::t("app", "求人情報の管理へ"), "#", ['class' => 'btn btn-simple btn-sm', 'onclick' => "javascript:window.open('" . Url::to(["secure/job/list"]) . "', 'job')"]);
            $content .= Html::tag("span", $button, ['style' => Html::cssStyleFromArray(['margin' => "0px 5px"])]);
            $content .= Html::endTag("div");

            $content .= Yii::t("app", "<b>検索キーコード・{clientChargePlanLabel}の一覧</b>：", ['clientChargePlanLabel' => $clientChargePlanLabel]);
            $content .= Html::beginTag("ul");
            $content .= Html::tag("li", Yii::t("app", "CSVを編集する際、検索キーコードや" . $clientChargePlanLabel . "を入力する際、下記をクリックすると一覧を参照することができます"));
            $content .= Html::endTag("ul");

            $content .= Html::beginTag("div", ['style' => Html::cssStyleFromArray(['margin' => '10px 0px'])]);
            $content .= Html::endTag("div");

            $content .= $this->render('_searchkey-csv');

            echo \yii\bootstrap\Alert::widget(['body' => $content, 'closeButton' => false, 'options' => ['class' => 'alert-info']]);

            echo FileInput::widget([
                'name' => 'file',
                'id' => 'csv-uploader',
                'options' => [
                    'multiple' => false,
                ],
                'pluginOptions' => [
                    'uploadUrl' => 'upload',
                    'previewFileType' => false,
                    'allowedPreviewTypes' => false,
                    'removeClass' => 'btn btn-danger',
                    'uploadClass' => 'btn btn-success',
                    'showUpload' => false,
                    'showRemove' => false,
                    'allowedFileExtensions' => ['csv'],
                    'layoutTemplates' => [
                        'actions' => '<div class="file-actions">' . PHP_EOL .
                            '    <div class="file-footer-buttons">' . PHP_EOL .
                            '        {upload}{delete}' . PHP_EOL .
                            '    </div>' . PHP_EOL .
                            '    <div class="clearfix"></div>' . PHP_EOL .
                            '</div>' . PHP_EOL
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
</div>