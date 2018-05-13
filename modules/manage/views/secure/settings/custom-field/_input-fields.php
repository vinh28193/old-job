<?php
use app\models\manage\CustomField;
use kartik\widgets\FileInput;
use proseeds\assets\PjaxModalAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use proseeds\widgets\TableForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use uran1980\yii\assets\TextareaAutosizeAsset;


/* @var $model CustomField */

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);

$this->registerJs('$("#modal").modal("show");');

PjaxModalAsset::register($this);
TextareaAutosizeAsset::register($this);

$tableForm = TableForm::begin([
    'id' => 'form',
    'action' => Url::to([$model->isNewRecord? 'create': 'update', 'id' => $model->id]),
    'options' => ['enctype' => 'multipart/form-data'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'validationUrl' => Url::to(['ajax-validation', 'id' => $model->id])
]);

$operation = $model->isNewRecord? Yii::t('app', '登録'): Yii::t('app', '変更');

Modal::begin([
    'id' => 'modal',
    'header' => Yii::t('app', 'カスタムフィールド{operation}', compact('operation')),
    'size' => Modal::SIZE_LARGE,
    'footer' => Html::button(Yii::t('app', '閉じる'), ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal']) . ' ' .
        Html::submitButton(Yii::t('app', '{operation}', compact('operation')), ['class' => 'btn btn-sm btn-primary submit']),
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

$tableForm->beginTable();

echo $tableForm->row($model, 'detail')->textarea();

echo $tableForm->row($model, 'url', ['options' => ['class' => 'form-group form-inline', 'enableAjaxValidation' => true]])
    ->layout(function () use ($model, $tableForm) {
        echo Html::encode(Yii::t('app', Yii::$app->request->getHostInfo()));
        echo Html::activeInput('text', $model, 'url', ['class' => 'form-control']);
    })->hint(Yii::t('app', '入力するURLは、実際に検索結果ページを開き、ブラウザからコピーするようにお願いします。'));

$pluginInit = !$model->pict ? [] : [
    'initialPreview' => [$model->srcUrl()],
    'initialPreviewAsData' => true,
];

echo $tableForm->row($model, 'pict')->widget(FileInput::className(), [
    'options' => ['accept' => 'image/*'],
    'pluginOptions' => array_merge([
        'showCaption' => false,
        'showUpload' => false,
        'showRemove' => true,
        'showClose' => false,
        'allowedFileExtensions' => CustomField::FILE_EXTENSIONS,
        'layoutTemplates' => ['footer' => '', 'actions' => '',],
    ], $pluginInit),
    'pluginEvents' => [
        'fileclear' => 'function() {  $("#pictDel").val(1); }',
    ]
])->hint(Yii::t('app', '推奨画像サイズ：300 x 100pixel<br />対応ファイル形式：jpg, jpeg, gif, png<br />最大容量：3MB'));
echo Html::hiddenInput('pushDeletePict', '0', ['id' => 'pictDel']);

echo $tableForm->row($model, 'valid_chk')->radioList(CustomField::validChkLabel());

$tableForm->endTable();

Modal::end();
TableForm::end();
Pjax::end();
