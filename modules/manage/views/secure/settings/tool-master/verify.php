<?php
/**
 * Created by PhpStorm.
 * User: KNakamoto
 * Date: 2016/02/18
 * Time: 16:10
 */

use app\models\manage\ManageMenuMain;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Html as bootstrapHtml;


/* @var $this yii\web\View */
\kartik\growl\GrowlAsset::register($this);
\app\assets\EventSourceAsset::register($this);
\yii\bootstrap\BootstrapPluginAsset::register($this);

$menu = ManageMenuMain::findFromRoute('manage/secure/settings/tool-master/index');
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;;

$workerUrl = Url::to(['verify-worker', 'filename' => $filename]);
$confirmUrl = Url::to(['confirm', 'filename' => $filename]);

$tooManyErrorMessage = Yii::t("app", "読み込みエラーが10件を超えているため、読み込みを停止しました。CSVの内容をご確認の上、アップロードを再度行ってください。");
$hasErrorMessage = Yii::t("app", "{errorCount}件のエラーが発生しています。CSVの内容をご確認ください。");
$loadingErrorMessage = Yii::t("app", "CSVファイルが存在しません。再度アップロードをお願いいたします。");
$emptyErrorMessage = Yii::t("app", "CSVファイルにデータが書かれていません。CSVの内容をご確認ください。");
$exceptionErrorMessage = Yii::t("app", "エラーが発生したため、CSVの検証を停止しました。");

$workerJs = <<<JS
(function($) {
    var eventSource = new EventSource("{$workerUrl}");

    var progressListener = function (event) {
        var data = JSON.parse(event.data);
        $(".total-count").text(data.totalCount);
        $(".progress-count").text(data.progress);
        $("#progress-bar").progressbar({value: data.progress / data.totalCount * 100});
    };

    var errorListener = function (event) {
        var data = JSON.parse(event.data);
        var alertTag = '<div class="alert alert-danger csv-error" role="alert">{message}</div>';
        data.error.forEach(function(message) {
            var alert = $(alertTag.replace(/{message}/g, message)).hide();
            $(".error-summary").prepend(alert.fadeIn());
        });
    };

    var completeListener = function (event) {
        eventSource.close();
        if ($(".csv-error").size() > 0) {
            var message = "{$hasErrorMessage}";
            $.notify({
                message: message.replace(/{errorCount}/, $(".csv-error").size())
            }, {
                type: "danger",
                delay: 0
            });
        } else {
            location.replace("{$confirmUrl}");
        }
    };

    var tooManyErrorListener = function (event) {
        eventSource.close();
        $.notify({
            message: "{$tooManyErrorMessage}"
        },{
            type: "danger",
            delay: 0
        });
    };

    var loadingErrorListener = function (event) {
        eventSource.close();
        $.notify({
            message: "{$loadingErrorMessage}"
        },{
            type: "danger",
            delay: 0
        });
    }

    var emptyErrorListener = function (event) {
        eventSource.close();
        $.notify({
            message: "{$emptyErrorMessage}"
        },{
            type: "danger",
            delay: 0
        });
    }

    var exceptionErrorListener = function (event) {
        eventSource.close();
        $.notify({
            message: "{$exceptionErrorMessage}"
        },{
            type: "danger",
            delay: 0
        });
    }

    eventSource.addEventListener("message", progressListener);
    eventSource.addEventListener("error", errorListener);
    eventSource.addEventListener("complete", completeListener);
    eventSource.addEventListener("tooManyError", tooManyErrorListener);
    eventSource.addEventListener("loadingError", loadingErrorListener);
    eventSource.addEventListener("emptyError", emptyErrorListener);
    eventSource.addEventListener("exceptionError", exceptionErrorListener);

    $(window).on("beforeunload", function (event) {
        eventSource.close();
    });
})(jQuery);
JS;

$this->registerJs($workerJs);
?>

<div class="tool-master-index">
        <?= Html::tag('h1', bootstrapHtml::icon($menu->icon_key) . Html::encode($this->title), ['class' => 'heading']) ?>

        <div class="col-md-10" role="complementary">
            <?php
                echo \yii\jui\ProgressBar::widget(['id' => 'progress-bar']);
                echo Html::tag("span", 0, ['class' => 'progress-count']) . '/' . Html::tag("span", 0, ['class' => 'total-count']) . Html::tag("span", Yii::t("app", "アップロードされたCSVをチェックしています..."), ['style' => Html::cssStyleFromArray(['margin' => "0px 10px"])]);
            ?>
            <?= Html::a("戻る", Url::to(["index"]), ['class' => 'btn btn-default center-block', 'style' => Html::cssStyleFromArray(['width' => '30%', 'margin' => '10px auto'])]) ?>
            <div class="error-summary"></div>
        </div>

</div>
