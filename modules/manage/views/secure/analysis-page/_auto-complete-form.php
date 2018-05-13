<?php

use yii\jui\AutoComplete;
use yii\web\JsExpression;
use app\models\manage\AccessLogSearch;

//アクセスログのオートコンプリート共通入力フォーム用ビュー
/* @var $form \yii\widgets\ActiveForm; */
/* @var $model \yii\db\ActiveRecord 表示データのモデルクラス(app\models\JobMasterDispなど) */
/* @var $columnName string */
/* @var $className string 発火対象のクラス名、指定がなければデフォルトでauto-complete-access-logが入る（同画面で2回以上使うときの対策） */
/* @var $url string  ajax通信で使用するURL */

$checkClassName = isset($className) ? $className : 'auto-complete-access-log';
$field = $form->field($model, $columnName);

echo $field->label(false)->widget(AutoComplete::className(), [
    'options' => [
        'class' => 'form-control input-txt input-txt-large ' . $checkClassName,
        'placeholder' => '',
    ],
    'clientOptions' => [
        'autoFill' => true,
        'source' => new JsExpression(AccessLogSearch::getScriptSource(".$checkClassName", $url)),
        'focus' => new JsExpression(AccessLogSearch::getScriptFocus()),
        'autoFocus' => true,
    ],
]);