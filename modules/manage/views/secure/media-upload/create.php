<?php

use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\FileInput;
use app\models\manage\MediaUpload;

/* @var $this yii\web\View */
/* @var $mediaUpload MediaUpload */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute('manage/secure/media-upload/list')->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
$css = <<<CSS
.file-preview-frame{
    display: block;
    height: auto;
    float: left;
}
CSS;
$this->registerCss($css);

//TODO:一時対応なので後で治す。エラーファイルのみ残す仕様にする。
$js = <<<JS
var isSuccess = true;
$('#imageFileField').on('fileuploaderror', function(event, file, previewId, index, reader) {
    isSuccess = false;
});
$('#imageFileField').on('filebatchuploadcomplete', function(event, file, previewId, index, reader) {
    if(isSuccess){
        $('#test01').attr('style','display: inherit;');
        $('#imageFileForm').fileinput('reset');
        $('#imageFileForm').fileinput('enable');
        $('#imageFileForm').fileinput('enable');
    }
});
$('#imageFileField').on('fileloaded', function(event, file, previewId, index, reader) {
    isSuccess = true;
    $('#test01').attr('style','display: none;');
});
JS;
$this->registerJs($js);

?>
<div class="container">
    <div class="row">
        <div class="col-md-12" role="complementary">
            <h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>
            <?php if (Yii::$app->session->getFlash('message')): ?>
                <p class="alert alert-warning"><?= Yii::$app->session->getFlash('message') ?></p>
            <?php endif; ?>
            <div id="test01" style="display: none;">
                <div class="jumbotron animated fadeIn text-center">
                    <h1><?= Yii::t('app', 'アップロード完了') ?></h1>
                    <button type="button" id="confirm-button" class="btn btn-primary" name="complete" onclick="javascript:location.href='/manage/secure/media-upload/list'">
                        <span class="glyphicon glyphicon-pencil"></span><?= Yii::t('app', '画像を確認する') ?>
                    </button>
                </div>
                <h1 class="heading"><?= Yii::t('app', '続けて画像をアップロードする') ?></h1>
            </div>
            <div class="corp-master-form">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form',
                    'action' => 'save',
                    'method' => 'post',
                    'options' => ['enctype' => 'multipart/form-data'],
                ]);
                echo $form->field($mediaUpload, 'imageFile', ['options' => ['id' => 'imageFileField']])->widget(FileInput::className(), [
                    'options' => ['accept' => 'image/*', 'multiple' => true, 'id' => 'imageFileForm'],
                    'pluginOptions' => [
                        'allowedFileExtensions' => MediaUpload::FILE_EXTENSIONS,
                        //TODO:@app\vendor\kartik-v\bootstrap-fileinput\js\fileinput.js
                        //TODO:のdefaultFileActionSettings.uploadClassから取得する書き方に変更する。
                        'uploadClass' => 'btn btn-default fileinput-upload fileinput-upload-button btn-primary',
                        'removeTitle' => Yii::t('app', 'ドラッグしたファイルを削除'),
                        'uploadTitle' => Yii::t('app', 'ドラッグしたファイルをアップロード'),
                        'uploadUrl' => 'save',
                        'showCaption' => false,
                        'showBrowse' => false,
                        'showCancel' => false,
                        'maxFileCount' => MediaUpload::MAX_FILES,
                        'maxFileSize' => MediaUpload::MAX_SIZE,
                        'maxPreviewFileSize' => MediaUpload::MAX_SIZE,
                    ],
                ])->label(false);
                ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>