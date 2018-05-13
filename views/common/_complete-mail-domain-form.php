<?php

use yii\jui\AutoComplete;
use yii\web\JsExpression;
use app\models\CompleteMailDomain;

//メールアドレスドメインの共通入力フォーム用ビュー
/* @var $form \app\common\KyujinForm|\proseeds\widgets\TableForm */
/* @var $model \yii\db\ActiveRecord 表示データのモデルクラス(app\models\JobMasterDispなど) */
/* @var $columnName string */
/* @var $className string 発火対象のクラス名、指定がなければデフォルトでauto-complete-mail-addressが入る（同画面で2回以上使うときの対策） */
/* @var $hintMessage string */

$applicationColumnSet = Yii::$app->functionItemSet->application->applyDispItems['mail_address'];
/* @var $applicationColumnSet \app\models\manage\ApplicationColumnSet */

$checkClassName = isset($className) ? $className : 'auto-complete-mail-address';
$field = $form->row($model, $columnName);
if (isset($hintMessage)) {
    $field->hint($hintMessage, ['tag' => 'p', 'class' => 'form-txt']);
}
echo $field->widget(AutoComplete::className(), [
    'options' => [
        'class' => 'form-control input-txt input-txt-large ' . $checkClassName,
        'placeholder' => $applicationColumnSet->column_explain,
    ],
    'clientOptions' => [
        'autoFill' => true,
        'source' => new JsExpression(CompleteMailDomain::getScriptSource(".$checkClassName")),
        'focus' => new JsExpression(CompleteMailDomain::getScriptFocus(".$checkClassName")),
        'autoFocus' => true,
    ],
]);