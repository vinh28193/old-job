<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/19
 * Time: 16:39
 */
use app\common\PostablePjax;
use app\common\widget\FormattedDatePicker;
use app\models\manage\Widget;
use app\models\manage\WidgetData;
use app\modules\manage\controllers\secure\WidgetDataController;
use kartik\widgets\FileInput;
use proseeds\assets\AdminAsset;
use proseeds\assets\BootBoxAsset;
use proseeds\widgets\TableForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $model WidgetData */
/* @var $tableForm \proseeds\widgets\TableForm */
/* @var $elements array */

if (!$model->widget_id) {
    exit();
}

/* @var $areaComp \app\components\Area */
$areaComp = Yii::$app->area;

if ($model->isNewRecord) {
    PostablePjax::begin(['id' => WidgetDataController::PJAX_ID]);
}

AdminAsset::register($this);
BootBoxAsset::confirmBeforeSubmit($this, $model->isNewRecord ? Yii::t('app', 'ウィジェットデータを登録してよろしいですか？') : Yii::t('app', 'ウィジェットデータを変更してもよろしいですか？'));

$areaJson = Json::encode(ArrayHelper::getColumn($areaComp->tenantArea, 'id'));
$syncUrlsJs = <<<JS
var areaIds = {$areaJson};
$("form").on("afterValidateAttribute", function() {
    if ($(".field-widgetdata-urls").children(".has-error").length > 0) {
        $("#form-urls-tr > th > div").removeClass("has-success");
        $("#form-urls-tr > th > div").addClass("has-error");
    } else {
        $("#form-urls-tr > th > div").removeClass("has-error");
        $("#form-urls-tr > th > div").addClass("has-success");
    }
})
JS;
$this->registerJs($syncUrlsJs);

// ファイルインプットのデザイン
$css = <<<CSS
.kv-preview-thumb div.kv-file-content {
    height: auto !important;
}
.kv-preview-thumb div.kv-file-content img{
    height: auto !important;
    max-height: 160px;
}
.checkbox + .checkbox {
    margin-top: 0px;
}
CSS;
$this->registerCss($css);

$tableForm = TableForm::begin([
    'id' => 'form',
    'action' => $model->isNewRecord ? 'create' : 'update?id=' . $model->id,
    'options' => ['enctype' => 'multipart/form-data'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'validationUrl' => Url::to(['ajax-validation', 'id' => $model->id]),
]);

echo $tableForm->form($model, 'widget_id')->hiddenInput();

$tableForm->beginTable();

// エリア選択
echo $tableForm->row($model, 'areaIds')->isRequired(true)->checkboxList($areaComp->listArray, ['class' => 'form-inline']);

// エリア毎のリンク先URL選択
if ($model->widget->element1 != Widget::ELEMENT_MOVIE) {
    echo $tableForm->row($model, 'urls', ['template' => "{th}\n{label}\n{/th}{td}\n{input}\n{hint}\n{/td}", 'options' => ['class' => 'form-horizontal']])
        ->layout(function () use ($model, $tableForm, $areaComp) {
            foreach ($areaComp->tenantArea as $area) {
                echo $tableForm->field(
                    $model,
                    'urls[' . $area->id . ']',
                    [
                        'template' => '{label}<div class="col-sm-9 field-widgetdata-urlarr">{input}<span class="glyphicon form-control-feedback" aria-hidden="true"></span>{error}</div>',
                        'errorOptions' => ['class' => 'error-block text-danger urlError'],
                    ]
                )->label($area->area_name, ['class' => 'col-sm-3 control-label'])->textInput(['class' => 'form-control url', 'placeholder' => Yii::t('app', '{max}文字未満', ['max' => 2000])]);
            }
        });
    echo $tableForm->breakLine();
}

// Widget要素
foreach ($model->widget->elements as $element) {
    switch ($element) {
        case Widget::ELEMENT_TITLE:
            echo $tableForm->row($model, 'title')->isRequired(true)->textInput(['placeholder' => Yii::t('app', '最大{max}文字', ['max' => 100])]);
            break;
        case Widget::ELEMENT_DESCRIPTION:
            echo $tableForm->row($model, 'description')->isRequired(true)->textInput(['placeholder' => Yii::t('app', '最大{max}文字', ['max' => 200])]);
            break;
        case Widget::ELEMENT_PICT:
            $pluginInit = $model->isNewRecord ? [] : [
                'initialPreview' => [$model->srcUrl()],
                'initialPreviewAsData' => true,
            ];
            echo $tableForm->row($model, 'pict')->isRequired(true)->widget(FileInput::className(), [
                'pluginOptions' => array_merge([
                    'showCaption' => false,
                    'showUpload' => false,
                    'showRemove' => false,
                    'showClose' => false,
                    'layoutTemplates' => ['footer' => '', 'actions' => '',],
                ], $pluginInit),
            ]);
            break;
        case Widget::ELEMENT_MOVIE:
            echo $tableForm->row($model, 'movieTag')->isRequired(true)->textInput(['placeholder' => Yii::t('app', '最大{max}文字', ['max' => 255])]);
            break;
        default:
            break;
    }
}

// 共通
echo $tableForm->row($model, 'disp_start_date', [
    'enableAjaxValidation' => true,
    'inputOptions' => ['class' => 'form-control limit_num']
])->widget(FormattedDatePicker::className(), [
    'options' => ['class' => 'form-control disp_start_date',]
]);
echo $tableForm->row($model, 'disp_end_date', [
    'enableAjaxValidation' => true,
    'inputOptions' => ['class' => 'form-control limit_num']
])->widget(FormattedDatePicker::className(), [
    'options' => ['class' => 'form-control disp_end_date',]
]);
echo $tableForm->row($model, 'sort')->isRequired(true)->textInput(['placeholder' => Yii::t('app', '最大値：{max}', ['max' => 255])]);
echo $tableForm->row($model, 'valid_chk')->radioList(WidgetData::validChkArray(), ['class' => 'form-group']);

$tableForm->endTable();
echo $this->render('/secure/common/_form-buttons.php', [
    'model' => $model,
    'cleaUrl' => $model->isNewRecord ? 'create' : '',
]);
TableForm::end();
if ($model->isNewRecord) {
    PostablePjax::end();
}
