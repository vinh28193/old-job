<?php
use uran1980\yii\assets\TextareaAutosizeAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use proseeds\widgets\TableForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\models\MailSend;

/* @var $model app\models\manage\SendMailSet */

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);

TextareaAutosizeAsset::register($this);
$this->registerJs('$("#modal").modal("show");');

$tableForm = TableForm::begin([
    'id' => 'form',
    'action' => Url::to(['update', 'id' => $model->id]),
    'options' => ['enctype' => 'multipart/form-data'],
    'tableOptions' => ['class' => 'table table-bordered'],
]);
Modal::begin([
    'id' => 'modal',
    'header' => Yii::t('app', 'メール設定変更'),
    'size' => Modal::SIZE_LARGE,
    'footer' => Html::button(Yii::t('app', '閉じる'), ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal']) . ' ' . Html::submitButton(Yii::t('app', '変更'), ['class' => 'btn btn-sm btn-primary submitUpdate']),
]);
?>

<?php
$tableForm->beginTable();
echo $tableForm->row($model, 'mail_name')->text();

echo $tableForm->row($model, 'mail_to')->text();

echo $tableForm->row($model, 'from_name')->isRequired(true)->textInput();

echo $tableForm->row($model, 'from_address')->textInput();

if ($model->mail_type_id == MailSend::TYPE_APPLY_TO_ADMIN) {
    echo $tableForm->row($model, 'notification_address')->hint(Yii::t('app', '入力が無い場合、求人原稿に登録した応募先メールアドレスのみに送信されます'))->textInput();
} elseif ($model->mail_type_id == MailSend::TYPE_INQUILY_NOTIFICATION) {
    echo $tableForm->row($model, 'notification_address')->textInput();
} elseif ($model->mail_type_id == MailSend::TYPE_JOB_REVIEW) {
    echo $tableForm->row($model, 'notification_address')->hint(Yii::t('app', '入力が無い場合、運営元に審査メールが送信されません。'))->textInput();
}

echo $tableForm->row($model, 'subject')->textInput();

echo $tableForm->row($model, 'contents')->textarea(['rows' => 8]);

echo $tableForm->row($model, 'mail_sign')->textarea(['rows' => 8]);

$tableForm->endTable();
?>
    <div class="replace_info">
        <?= Html::a(
            '<span class="glyphicon glyphicon-list-alt"></span>' . Yii::t('app', '使用できる置換文字列'),
            '#collapse_replace_info-' . $model->id,
            [
                'role' => 'button',
                'data-toggle' => 'collapse',
                'aria-expanded' => 'false',
                'aria-controls' => 'collapse',
                'class' => 'pull-right',
            ]
        ); ?>
        <br>
        <?= Html::tag(
            'div',
            $this->render('_collapse_replace_info', ['model' => $model]),
            [
                'id' => 'collapse_replace_info-' . $model->id,
                'class' => 'collapse clearfix',
            ]
        ); ?>
    </div>

<?php
Modal::end();
TableForm::end();
Pjax::end();
