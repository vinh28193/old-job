<?php

use app\modules\manage\models\JobReview;
use proseeds\assets\PjaxModalAsset;

use proseeds\widgets\TableForm;
use uran1980\yii\assets\TextareaAutosizeAsset;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/** @var JobReview $jobReview */
/** @var yii\web\View $this */

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.showModal',
]);
PjaxModalAsset::register($this);
TextareaAutosizeAsset::register($this);

$this->registerJs('$("#modal").modal("show");');

/** @var TableForm $tableForm */
$tableForm = TableForm::begin([
    'id' => 'review-form',
    'action' => '/manage/secure/job-review/review',
    'options' => ['enctype' => 'multipart/form-data'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'enableAjaxValidation' => false,
    'validationUrl' => '/manage/secure/job-review/ajax-validation',
]);
Modal::begin([
    'id' => 'modal',
    'header' => Yii::t('app', '審査状況更新'),
    'footer' => Html::button(
        Yii::t('app', '閉じる'),
        ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal']
    ) . ' ' . Html::submitButton(Yii::t('app', '更新'), ['class' => 'btn btn-sm btn-primary submitUpdate']),
    'size' => Modal::SIZE_LARGE,
]);

$tableForm->beginTable();

// 審査状況
// 裏で更新された場合を考慮して、現在ステータスをhiddenで持つ
echo $tableForm->row($jobReview, 'job_review_status_id', ['enableAjaxValidation' => true])->textWithHiddenInput();

// 審査OK/NG
echo $tableForm->row($jobReview, 'review', ['template' => "{th}\n{label}\n{/th}\n{td}\n{input}\n{/td}"])->layout(function () use ($tableForm, $jobReview) {
    echo $tableForm->form($jobReview, 'review')->radioList([JobReview::REVIEW_OK => Yii::t('app', '審査OK'), JobReview::REVIEW_NG => Yii::t('app', '審査NG')]);
    echo Html::beginTag('hr', ['class' => 'mgt10 mgb10']);
    echo Html::beginTag('p', ['class' => 'ft12 text-red']);
    echo $jobReview->notificationHint();
    echo Html::endTag('p');
});

// 審査コメント
echo $tableForm->row($jobReview, 'comment')->textarea();

// 求人ID(hidden)
echo Html::activeHiddenInput($jobReview, 'job_master_id');

$tableForm->endTable();

// 審査履歴
// 原稿登録画面でも使用するので、折り畳み識別IDを振り分けるために「modal」変数を送っている
echo $this->render('/secure/job-review/common/_review-history', ['id' => $jobReview->job_master_id, 'modal' => '-modal']);

Modal::end();
TableForm::end();
Pjax::end();
