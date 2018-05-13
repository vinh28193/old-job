<?php

use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use proseeds\widgets\TableForm;
use app\models\manage\ApplicationStatus;
use yii\helpers\Url;
use proseeds\assets\BootBoxAsset;

/* @var $this yii\web\View */
/* @var $model yii\db\BaseActiveRecord */
/* @var $type string */         // 表示するテーブルフォームの種類を指定している
/* @var $label string */        // モーダルのタイトル
/* @var $id string */           //アクションメソッドにおける、更新対象のテーブルのidの指定

//2つ入力フォームがあるため、メッセージを切り替えている。
BootBoxAsset::confirmBeforeSubmit($this, Yii::t("app", "応募者へメールを送信してもよろしいですか？"), '#mail_send');
BootBoxAsset::confirmBeforeSubmit($this, Yii::t("app", "応募ステータスを変更してもよろしいですか？"), '#status_change');

//TODO:他でも需要がありそうであれば、汎用的な書き方にする
?>

<?php
$tableForm = TableForm::begin([
    'action' => ($type == 'mail') ? Url::to(['mail', 'id' => $id]) : Url::to(['update', 'id' => $id]),
    'id' => ($type == 'mail') ? 'mail_send' : 'status_change' , // BootBoxAssetのメッセージの切り替え用にIDを振り分けている
    'tableOptions' => ['class' => 'table table-bordered'],
]);
$fieldOptions = ['tableHeaderOptions' => ['class' => 'm-column']];
Modal::begin([
    'id' => 'modal-' . $model->id,
    'header' => '<h2>' .(($type == 'mail') ? Yii::t('app', 'メール送信') : Yii::t('app', '応募ステータス変更')) . '</h2>',
    'toggleButton' => ['label' => $label, 'class' => 'btn btn-primary btn-lg'],
    'footer' =>
        Html::button(Yii::t('app', '閉じる'), ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal']) .
        ' ' . Html::submitButton(($type == 'mail') ? Yii::t('app', '送信') : Yii::t('app', '変更') , ['class' => 'btn btn-sm btn-primary submitUpdate']),
]);
$tableForm->beginTable();
if ($type == 'mail') {
    // 応募者へのメール送信用テーブルフォーム
    echo $tableForm->row($model, 'mail_title', $fieldOptions)->textInput();
    echo $tableForm->row($model, 'from_mail_address', $fieldOptions)->textInput(['value' => Yii::$app->user->identity->mail_address]);
    echo $tableForm->row($model, 'mail_body', $fieldOptions + ['tableDataOptions' => ['style' => 'Auto']])->textarea(['class' => "form-control", 'rows' => '7']);
} else {
    // 応募者情報の詳細更新用のテーブルフォーム
    echo $tableForm->row($model, 'application_status_id', $fieldOptions)->dropDownList(ApplicationStatus::getDropDownList(null));
    echo $tableForm->row($model, 'application_memo', $fieldOptions)->textarea(['class' => "form-control", 'rows' => '7']);
}
echo Html::hiddenInput('complete');
$tableForm->endTable();
Modal::end();
TableForm::end();
?>