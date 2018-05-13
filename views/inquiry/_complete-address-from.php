<?php
use yii\web\View;
use yii\helpers\Html;
use app\assets\AjaxZip3Asset;

/**
 * @var $this View
 * @var $form \app\common\KyujinForm
 * @var $model \yii\base\Model
 * @var $attribute string column name
 * @var $columnSet \app\models\manage\BaseColumnSet
 * @var $dependAttribute string
 * @var $hintMessage string
 */
// Register Js
AjaxZip3Asset::register($this);
// Get Depend Attribute
$parentName = Html::getInputName($model, $dependAttribute);
// Input
$field = $form->row($model, $attribute);
$childName = Html::getInputName($model, $attribute);
if (isset($hintMessage)) {
    $field->hint($hintMessage, ['tag' => 'p', 'class' => 'form-txt']);
}
echo $field->textInput(['placeholder' => $columnSet->placeholder]);
$js = <<<JS
jQuery("input[name = '{$parentName}']").keyup(function(e) {
    AjaxZip3.zip2addr(this, '', '{$childName}', '{$childName}');   
});
JS;
$this->registerJs($js);
