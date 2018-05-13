<?php
use app\assets\EventSourceAsset;
use app\models\manage\ManageMenuMain;
use kartik\growl\GrowlAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Html as bootstrapHtml;
use yii\jui\ProgressBar;

GrowlAsset::register($this);
EventSourceAsset::register($this);
BootstrapPluginAsset::register($this);

$menu = ManageMenuMain::findFromRoute('manage/secure/settings/custom-field/list');
$this->title = Yii::t('app', 'カスタムフィールドCSV一括登録・変更');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = ['label' => $menu->title, 'url' => ['secure/settings/custom-field/list']];
$this->params['breadcrumbs'][] = $this->title;

$workerUrl = Url::to(['register-worker', 'filename' => $filename]);
$completeUrl = Url::to(['complete']);

$loadingErrorListener = Yii::t('app', 'CSVファイルが存在しません。再度アップロードをお願いいたします。');

$workerJs = <<<JS
(function($) {
    var eventSource = new EventSource("{$workerUrl}");
    var totalCount = 0;

    var progressListener = function (event) {
        var data = JSON.parse(event.data);
        totalCount = data.totalCount;
        $('.total-count').text(data.totalCount);
        $('.progress-count').text(data.progress);
        $('#progress-bar').progressbar({value: data.progress / data.totalCount * 100});
    };

    var errorListener = function (event) {
        eventSource.close();
        var data = JSON.parse(event.data);
        var alertTag = '<div class="alert alert-danger" role="alert">{message}</div>';
        var alert = $(alertTag.replace(/{message}/g, data.error)).hide();
        $('.error-summary').prepend(alert.fadeIn());
        $('.back-button').show();
    }

    var completeListener = function (event) {
        eventSource.close();
        location.href = "{$completeUrl}?count=" + totalCount;
    }

    var loadingErrorListener = function (event) {
        eventSource.close();
        $.notify({
            message: "{$loadingErrorListener}"
        },{
            type: 'danger',
            delay: 0
        });
    }

    eventSource.addEventListener('message', progressListener);
    eventSource.addEventListener('error', errorListener);
    eventSource.addEventListener('complete', completeListener);
    eventSource.addEventListener('loadingError', loadingErrorListener);

    $(window).on('beforeunload', function (event) {
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
                echo ProgressBar::widget(['id' => 'progress-bar']);
            echo Html::tag('span', 0, ['class' => 'progress-count']) . '/' . Html::tag('span', 0,
                    ['class' => 'total-count']) . Html::tag('span', Yii::t('app', 'データを登録しています...'),
                    ['style' => Html::cssStyleFromArray(['margin' => '0px 10px'])]);
            ?>
            <?= Html::a(Yii::t('app', '戻る'), Url::to(['csv']), [
                'class' => 'btn btn-info center-block back-button',
                'style' => Html::cssStyleFromArray(['width' => '30%', 'margin' => '10px auto', 'display' => 'none'])
            ]) ?>
            <div class="error-summary"></div>
        </div>

</div>
