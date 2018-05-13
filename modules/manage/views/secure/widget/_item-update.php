<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/05/25
 * Time: 10:22
 */
use yii\bootstrap\Modal;
use yii\helpers\Html;
use proseeds\widgets\TableForm;
use proseeds\assets\AdminAsset;
use app\models\manage\Widget;

$this->registerCss('.popover{
    max-width:100%; /* Max Width of the popover (depending on the container!) */
}');
$this->registerJs('$("#modal-widget").modal("show");');
AdminAsset::register($this);
$tableForm = TableForm::begin([
    'id' => 'widgetForm',
    'tableOptions' => ['class' => 'table table-bordered'],
    'options' => ['enctype' => 'multipart/form-data'],
]);
Modal::begin([
    'id' => 'modal-widget',
    'header' => Yii::t('app', '変更'),
    'size' => Modal::SIZE_LARGE,
    'footer' => Html::submitButton(Yii::t('app', '変更'), [
        'id' => 'updateButton',
        'class' => 'btn btn-primary submitUpdate',
    ]),
]);
echo Html::hiddenInput('submitButton', 'default', ['id' => 'submitButton']);
?>
<?php
$tableForm->beginTable();
echo Html::hiddenInput('id', $model->id);
echo $tableForm->row($model, 'widget_name')->textInput();
echo $tableForm->row($model, 'widgetDataPattern', [
    'hint' => $this->render('_pcpatternhint'),
    'popoverHintOptions' => [
        'dataContainer' => '.modal-content',
        'placement' => 'right',
        'dataHtml' => true,
    ],
])->dropDownList(Widget::getWidgetDataPatternLabels());
echo $tableForm->row($model, 'style_sp', [
    'hint' => $this->render('_sppatternhint'),
    'popoverHintOptions' => [
        'dataContainer' => '.modal-content',
        'placement' => 'right',
        'dataHtml' => true,
    ],
])->dropDownList(Widget::getStyleSpLabels());
echo $tableForm->row($model, 'is_disp_widget_name')->radioList(Widget::getIsDispWidgetNameLabels());
echo $tableForm->row($model, 'data_per_line_pc')->dropDownList(Widget::getDataPerLinePcLabels());
echo $tableForm->row($model, 'data_per_line_sp')->dropDownList(Widget::getDataPerLineSpLabels());
$tableForm->endTable();
?>
<?php
Modal::end();
TableForm::end();

$widgetDataPattern1 = Widget::WIDGET_DATA_PATTERN_1;
$widgetDataPattern2 = Widget::WIDGET_DATA_PATTERN_2;
$widgetDataPattern7 = Widget::WIDGET_DATA_PATTERN_7;

$js = <<<JS
function showWidgetModal(){
    var widgetDataPattern = $('#widget-widgetdatapattern').val();
    if ( widgetDataPattern == $widgetDataPattern1 || widgetDataPattern == $widgetDataPattern2){
        $('#widgetForm-style_sp-tr').show();
        $('#widgetForm-data_per_line_pc-tr').show();
        $('#widgetForm-data_per_line_sp-tr').show();
    } else if (widgetDataPattern == $widgetDataPattern7){
        $('#widgetForm-style_sp-tr').hide();
        $('#widgetForm-data_per_line_pc-tr').hide();
        $('#widgetForm-data_per_line_sp-tr').hide();
    } else {
        $('#widgetForm-style_sp-tr').hide();
        $('#widgetForm-data_per_line_pc-tr').show();
        $('#widgetForm-data_per_line_sp-tr').show();
    }
}
showWidgetModal();
$('#widget-widgetdatapattern').change(function(){
    showWidgetModal();
});
JS;
$this->registerJs($js);
$ajaxJs = <<<JS
$('#widgetForm').on('beforeSubmit', function(e){
    $.ajax({
        url : "/manage/secure/widget/update",
        type : "post",
        dateType : "text",
        data : $("#widgetForm").serialize(),
        success : function (data){
            $("#modal-widget").modal("hide");
            $("#showMessage").html('<p class="alert alert-warning">' + data.message + '</p>');
            var value = data.widgetName;
            $("#item-{$model->id}").html(value);
        }
    });
    e.stopImmediatePropagation();
    return false;
});
JS;
$this->registerJs($ajaxJs);
