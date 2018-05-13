<?php
use app\models\manage\ManageMenuMain;
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;;

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

    <div class="tool-master-index">
        <?= Html::tag('h1', Html::icon($menu->icon_key) . Html::encode($this->title), ['class' => 'heading']) ?>

        <div class="col-md-10" role="complementary">
            <?php
            if (Yii::$app->session->hasFlash("csvError")) {
                foreach((array)Yii::$app->session->getFlash("csvError") as $error) {
                    echo \yii\bootstrap\Alert::widget(['body' => $error, 'options' => ['class' => 'alert-danger']]);
                }
            }

            $content = Yii::t("app", "<b>変更手順</b>：");
            $content .= Html::beginTag("ul");
            $content .= Html::tag("li", Yii::t("app", "以下の「CSVダウンロード」をクリックします"));
            $content .= Html::tag("li", Yii::t("app", "「CSVの入力方法」に従って、ダウンロードされたCSVを編集します"));
            $content .= Html::tag("li", Yii::t("app", "編集したCSVをこの画面からアップロードします"));
            $content .= Html::endTag("ul");

            $content .= Html::beginTag("div", ['style' => Html::cssStyleFromArray(['margin' => '10px 0px'])]);
            $buttons = [
                Html::a(Html::icon("file") . Yii::t("app", "CSVダウンロード"), Url::to(["csv-download"]), ['class' => 'btn btn-info']),
            ];
            foreach($buttons as $button) {
                $content .= Html::tag("span", $button, ['style' => Html::cssStyleFromArray(['margin' => "0px 5px"])]);
            }
            $content .= Html::a(Html::icon("question-sign") . Yii::t("app", "CSVの入力方法"), "#", ['class' => 'btn btn-simple', 'onclick' => "javascript:window.open('" . Url::to(['help']) . "', 'help', 'width=700,height=800')"]);
            $content .= Html::endTag("div");

            echo \yii\bootstrap\Alert::widget(['body' => $content, 'closeButton' => false, 'options' => ['class' => 'alert-info']]);

            echo FileInput::widget([
                'id' => 'csv-uploader',
                'name' => 'file',
                'options' => [
                    'multiple' => false,
                ],
                'pluginOptions' => [
                    'uploadUrl' => Url::to(['/manage/secure/settings/tool-master/csv-upload']),
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

