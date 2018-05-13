<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/09/23
 * Time: 19:58
 */

use app\models\manage\ApplicationColumnSet;
use app\models\manage\ApplicationColumnSubset;
use app\models\manage\BaseColumnSet;
use app\models\manage\JobColumnSet;
use app\models\manage\JobColumnSubset;
use app\modules\manage\controllers\secure\OptionBaseController;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $subsetModel JobColumnSubset|ApplicationColumnSubset|InquiryColumnSubset */
/* @var $model JobColumnSet|ApplicationColumnSet|InquiryColumnSet */

$subsetAttribute = '["+subsetindex+"]subset_name';
$inputId = Html::getInputId($subsetModel, $subsetAttribute);
$inputName = Html::getInputName($subsetModel, $subsetAttribute);
$triggerInputName = Html::getInputName($model, 'data_type');
$eachInputId = str_replace('subsetindex', 'i', $inputId);
$tableRowId = 'updateForm-subset_name-tr';
$ulId = OptionBaseController::SUBSET_UL_ID;
$formId = OptionBaseController::UPDATE_FORM_ID;
$check = BaseColumnSet::DATA_TYPE_CHECK;
$radio = BaseColumnSet::DATA_TYPE_RADIO;
$text = BaseColumnSet::DATA_TYPE_TEXT;
$subsetValidationMessages = [
    'req' => Yii::t('app', '選択肢項目名は必須項目です。'),
    'str' => Yii::t('app', '選択肢項目名は文字列にしてください。'),
    'max' => Yii::t('app', '選択肢項目名は255文字以下で入力してください。'),
    'overlap' => Yii::t('app', '選択肢項目名が重複しています。'),
];
$buttonValue = [
    'add' => Yii::t('app', '追加'),
    'remove' => Yii::t('app', '削除'),
];
$this->registerJs('var subsetindex = 0;', $this::POS_END);
$addFieldJs = <<<JS
(function ($) {

var addButton = '<input type="button" class="btn btn-sm btn-default add" value="{$buttonValue['add']}">';
var removeButton = '<input type="button" class="btn btn-sm btn-default remove" value="{$buttonValue['remove']}">';
function add() {
  // fieldの追加
  var buttons;
  if(subsetindex == 0){
    buttons = addButton;
  } else if($(".add-input-form").length >= 49) {
    buttons = removeButton;
  } else {
    buttons = removeButton + addButton;
  }
  var liElement = document.createElement("li");
  liElement.innerHTML = "<div class=\"add-input-form field-{$inputId} required\"><input type=\"text\" id=\"{$inputId}\" class=\"form-control\" name=\"{$inputName}\"><span class=\"glyphicon form-control-feedback\" aria-hidden=\"true\"></span><div class=\"error-block text-danger\"></div></div><div class=\"add-input-btn-wrap\">" + buttons + "</div>";
  var parentObject = document.getElementById("{$ulId}");
  parentObject.appendChild(liElement);
  // validationAttributeの追加
  var attribute = {
    "id": "{$inputId}",
    "name": "{$subsetAttribute}",
    "container": ".field-{$inputId}",
    "input": "#{$inputId}",
    "error": ".error-block.text-danger",
    "validateOnType": true,
    "validate": function(attribute, value, messages, deferred, \$form) {
      yii.validation.required(value, messages, {
        "message": "{$subsetValidationMessages['req']}"
      });
      yii.validation.string(value, messages, {
        "message": "{$subsetValidationMessages['str']}",
        "max": 255,
        "tooLong": "{$subsetValidationMessages['max']}",
        "skipOnEmpty": 1
      });
      var checkTarget = $('.overlap-subset_name').find("input[type='text']");
      var targetArray = checkTarget.get();
      var index = $(checkTarget).index($('#' + attribute.id));
      for (var i = 0; i < targetArray.length; i++) {
          if (i != index && targetArray[i].value == value) {
              messages.push("{$subsetValidationMessages['overlap']}");
              break;
          }
      }
    },
    "validateMarkOptions": {
      "class": "glyphicon form-control-feedback",
      "aria-hidden": "true"
    },
    "successMarkClass": "glyphicon-ok",
    "failMarkClass": "glyphicon-remove"
  }
  $("#{$formId}").yiiActiveForm("add", attribute)
  subsetindex++;
}
// addボタン押下時
$(document).on("click",".add", function() {
  // 初期（removeボタンがひとつも無い状態）
  if ($(".remove").length === 0) {
    // removeボタンを追加する
    $(this).before(removeButton);
  }
  // fieldを追加
  add();
  // 押されたaddボタンを削除
  $(this).remove();
});
// removeボタン押下時
$(document).on("click",".remove", function() {
  // fieldを削除
  $(this).closest("li").remove();
  // 削除によってinputがひとつになる時
  var removeButtons = $(".remove");
  if (removeButtons.length === 1) {
    // removeボタンを削除
    removeButtons.remove();
  }
  // 削除によってaddボタンがなくなってしまう時
  if($(".add").length === 0){
    // 一番最後にaddボタンを追加
    $("#{$ulId}").find(":last").after(addButton);
  }
});
// ラジオボタン操作時
$(document).on("change", "[name='{$triggerInputName}']", function() {
  if ($(this).val() == '{$check}' || $(this).val() == '{$radio}') {
    if ($(".add-input-form").length == 0) {
      $("#{$tableRowId}").show();
      add();
      $("#updateForm-max_length-tr").hide();
      $("#updateForm-column_explain-tr").hide();
    }
  } else {
    $("#{$tableRowId}").hide();
    $("#updateForm-max_length-tr").show();

    $("#updateForm-column_explain-tr").show();
    $("#{$ulId}").find("li").remove();
    for (var i = 0 ; i <= subsetindex ; i++){
      $("#{$formId}").yiiActiveForm("remove", "{$eachInputId}");
    }
    subsetindex = 0;

  }
});

})(window.jQuery);
JS;
$this->registerJs($addFieldJs);
