<?php

use proseeds\assets\BootBoxAsset;
use uran1980\yii\assets\TextareaAutosizeAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $jobReview app\modules\manage\models\JobReview */
/* @var $isUpdate boolean */

$this->title = Yii::t('app', '求人原稿情報 - 完了');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '求人原稿情報'), 'url' => Url::to(['secure/job/index'])];
$this->params['breadcrumbs'][] = Yii::t('app', '完了');

TextareaAutosizeAsset::register($this);
BootBoxAsset::confirmBeforeSubmit($this, Yii::t('app', '審査依頼を行います。よろしいですか？'));
?>

<h1 class="heading"><span class="glyphicon glyphicon-list-alt"></span><?= Html::encode($this->title) ?></h1>

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <div class="jumbotron animated fadeIn pdt10 pdb10 pdl10">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12">
                            <p class="mg0 text-left"><?= $isUpdate ? Yii::t('app', '求人原稿情報の内容が変更されました。') : Yii::t('app', '求人原稿情報が登録されました。') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <p class="ft16 mgb20">
                <?= Yii::t('app', '審査依頼ボタンをクリックして審査依頼を行ってください。') ?><br>
                <?= Yii::t('app', '代理店・運営元にて原稿の確認を行います。') ?>
            </p>
            <?php
            $formOption = [
                'id' => 'review-request-form',
                'action' => 'request-complete',
                'options' => [
                    'accept-charset' => 'UTF-8',
                    'class' => 'form-inline',
                    'role' => 'form',
                ],
                'fieldConfig' => [
                    'template' => "{input}\n{error}",
                    'labelOptions' => ['class' => 'control-label'],
                ],
                'enableAjaxValidation' => false,
                'validationUrl' => 'ajax-validation',
            ];

            $form = ActiveForm::begin($formOption);

            // コメント
            echo $form->field($jobReview, 'comment', ['options' => ['class' => 'w70 center-block']])->textarea(['class' => 'mgb10 w70l w100m ', 'placeholder' => Yii::t('app', '審査依頼コメントを入力してください（任意）')]);

            // 求人ID(hidden)
            echo $form->field($jobReview, 'job_master_id', ['options' => ['class' => 'mgb0']])->hiddenInput();
            // 現審査ステータス(hidden)
            echo $form->field($jobReview, 'job_review_status_id', ['options' => ['class' => 'mgb0'], 'enableAjaxValidation' => true])->hiddenInput();
            ?>
            <div class="center-block w50 mgb40">
                <?= Html::submitButton(
                    Html::icon('envelope') . Yii::t('app', '審査依頼する'),
                    ['class' => 'btn btn-block btn-danger btn-lg']
                );?>
            </div>
            <div class="mgb40">
                <?php // TODO 画像の配置場所。審査フロー画像に直接アクセスするとフロントから見えてしまうが問題ないか ?>
                <img class="center-block w70l w90m w w80s" src="/pict/flow.png">
            </div>
            <?php ActiveForm::end(); ?>
            <div class="col-sm-12 col-md-6">
                <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(['secure/job/index']) ?>" role="button"><?= Yii::t('app', '求人原稿情報一覧へ') ?></a></p>
            </div>
            <div class="col-sm-12 col-md-6">
                <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(['secure/']) ?>" role="button"><?= Yii::t('app', 'トップページへ戻る') ?></a></p>
            </div>
        </div>
    </div>
</div>