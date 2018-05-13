<?php

use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\helpers\Url;
use proseeds\assets\BootBoxAsset;
use proseeds\widgets\TableForm;
use yii\web\View;
use kartik\widgets\FileInput;
use app\common\Helper\JmUtils;

/* @var $this yii\web\View */
/* @var $model app\models\manage\HeaderFooterSetting */

$this->title = ManageMenuMain::findFromRoute(Url::toRoute('update'))->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

BootBoxAsset::confirmBeforeSubmit($this, Yii::t('app', 'ヘッダー及びフッターを変更してもよろしいですか？'));

// todo php共々asset化してwidget化する必要がある
// todo validation失敗時にsubmitTypeが変になる
// @see @app/controllers/TopController.php:155
// @see @app/modules/manage/views/secure/free-content/_form.php
// @see @app/modules/manage/views/secure/job/form/_form.php
$newWindowSubmit = <<<JS
(function($) {
    $('#form').on('beforeSubmit', function(e){
        //どのsubmitボタンをクリックしたのか
        var submitType = $('#submitType').val();
        if(submitType == 'pcSubmit' || submitType == 'spSubmit'){
            var targetName = '';
            var actionUrl = '';
            if(submitType == 'spSubmit'){
                targetName = 'sp_preview';
                actionUrl = "/manage/top/preview?mode=Mobile";
                window.open("about:blank",targetName,"width=600, height=950, scrollbars=yes");
            } else if(submitType == 'pcSubmit'){
                targetName = 'pc_preview';
                actionUrl = "/manage/top/preview?mode=PC";
                window.open("about:blank",targetName,"width=1000, height=950, scrollbars=yes");
            } else {
                return false;
            }
            var target = document.input_form.target;
            var action = document.input_form.action;
    
            document.input_form.target = targetName;
            document.input_form.action = actionUrl;
            document.input_form.submit();

            document.input_form.target = target;
            document.input_form.action = action;
            document.getElementById("submitType").value = 'default';
            
            //以降の処理を中断
            e.stopImmediatePropagation();
        } else {
            // ポスト前にbase64を消去
            $('#base64Url').val('');
        }
        return false;
    });

    /*
     * 添付した画像をbase64変換して、プレビュー用に格納
     * IEで別タブ・別ウィンドウへのファイルpostをした場合、ファイルが
     * 空になってしまうため、base64変換をしている。
     */
    $('#headerfootersetting-imagefile').change(function(evt){
        var file = evt.target.files[0];
        var fr = new FileReader();
        fr.readAsDataURL(file);
        fr.onload = function(evt) {
            $('#base64Url').val(evt.target.result);
        }
        // 'logo_file_name'に必須チェックを掛けているが、それを回避するため
        $('#headerfootersetting-logo_file_name').val('dummy_name.png');
    });
})(jQuery);
JS;
$this->registerJs($newWindowSubmit, View::POS_END);

$tableForm = TableForm::begin([
    'id' => 'form',
    'action' => 'update?id=' . $model->id,
    'options' => ['enctype' => 'multipart/form-data', 'name' => 'input_form'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'validationUrl' => Url::to(['ajax-validation', 'id' => $model->id]),
    'enableClientValidation' => true,
]);

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

// ワンエリア設定されていない場合は、全国トップページへのリンク
$urlText = '';
/** @var \app\components\Area $area */
$area = Yii::$app->area;
if (!$area->isOneArea()) {
    $urlText = '※全国トップページへのリンク：' . Url::to(['//top/zenkoku'], true);
}
$urlText2 = '※掲載の問い合わせへのリンク：' . Url::to(['//inquiry'], true);
$headerImg = Html::img('@web/pict/header.png', ['width' => '100%', 'alt' => '']);
$footerImg = Html::img('@web/pict/footer.png', ['width' => '100%', 'alt' => '']);
$description = <<<TEXT
求職者画面のヘッダーフッターの内容を設定します。
設定できる内容
ヘッダー：ロゴ、テキストリンク、電話番号
フッター：テキストリンク、コピーライト
$urlText
$urlText2
※テキストのみの入力も可能です。

<b>【ヘッダーの表示】</b>
$headerImg

<b>【フッターの表示】</b>
$footerImg
TEXT;

?>
<h1><?= Html::icon('wrench') . Html::encode($this->title) ?></h1>
<?php
if (Yii::$app->session->getFlash('updateError')):
    echo Html::tag('p', Yii::$app->session->getFlash('updateError'), ['class' => 'alert alert-danger']);
endif;
?>
<div class="container">
    <div class="row">
        <div class="col-md-10" role="complementary">
            <p class="alert alert-warning"><?= Yii::t('app', nl2br($description)) ?></p>
            <div class="headerFooter-form">
                <?php
                echo Html::tag('h3', Yii:: t('app', 'ヘッダー'), ['class' => 'border_bottom']);
                $tableForm->beginTable();
                $pluginInit = $model->isNewRecord ? [] : [
                    'initialPreview' => [$model->srcUrl()],
                    'initialPreviewAsData' => true,
                ];
                echo $tableForm->row($model, 'imageFile')->isRequired(true)->widget(FileInput::className(), [
                    'pluginOptions' => array_merge([
                        'showCaption' => false,
                        'showUpload' => false,
                        'showRemove' => false,
                        'showClose' => false,
                        'layoutTemplates' => ['footer' => '', 'actions' => '',],
                    ], $pluginInit),
                ]);
                echo $tableForm->form($model, 'logo_file_name')->hiddenInput();
                echo Html::activeHiddenInput($model, 'base64Url', ['id' => 'base64Url']);
                for ($i = 1; $i <= 10; $i++) {
                    echo $tableForm->row($model, 'header_name' . $i)->layout(function () use ($model, $tableForm, $i) {
                        echo $tableForm->labelForm($model, 'header_text' . $i, ['inputOptions' => ['class' => 'form-control']])->textInput();
                        echo $tableForm->labelForm($model, 'header_url' . $i, ['inputOptions' => ['class' => 'form-control']])->textInput();
                    });
                }
                echo $tableForm->row($model, 'tel_no', ['inputOptions' => ['class' => 'form-control']])->textInput();
                echo $tableForm->row($model, 'tel_text', ['inputOptions' => ['class' => 'form-control']])->textInput();
                $tableForm->endTable();
                echo Html::tag('h3', Yii:: t('app', 'フッター'), ['class' => 'border_bottom']);
                $tableForm->beginTable();
                for ($i = 1; $i <= 10; $i++) {
                    echo $tableForm->row($model, 'footer_name' . $i)->layout(function () use ($model, $tableForm, $i) {
                        echo $tableForm->labelForm($model, 'footer_text' . $i, ['inputOptions' => ['class' => 'form-control']])->textInput();
                        echo $tableForm->labelForm($model, 'footer_url' . $i, ['inputOptions' => ['class' => 'form-control']])->textInput();
                    });
                }
                echo $tableForm->row($model, 'copyright', ['inputOptions' => ['class' => 'form-control']])->textInput();
                $tableForm->endTable();

                echo '<p class="text-center">';
                echo Html::submitButton(
                    Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . Yii::t('app', 'PC版プレビュー'),
                    ['class' => 'btn btn-warning mgr20', 'onclick' => 'document.getElementById("submitType").value = "pcSubmit";']);
                echo Html::submitButton(
                    Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . Yii::t('app', 'スマホ版プレビュー'),
                    ['class' => 'btn btn-warning mgr20', 'onclick' => 'document.getElementById("submitType").value = "spSubmit";']
                );
                echo Html::submitButton('更新', ['class' => 'btn btn-primary', 'name' => 'complete']);
                echo Html::hiddenInput('submitType', 'default', ['id' => 'submitType']);
                echo '</p>';
                TableForm::end();
                ?>
            </div>
        </div>
    </div>
</div>