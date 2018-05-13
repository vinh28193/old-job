<?php

use app\common\KyujinForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\ApplyAsset;
use app\assets\ConfirmBoxAsset;
use uran1980\yii\assets\TextareaAutosizeAsset;

/**
 * @var $this    \yii\web\View
 * @var $inquiry \app\models\manage\InquiryMaster
 * @var $policy  \app\models\manage\Policy
 */

ApplyAsset::register($this);
ConfirmBoxAsset::transitionConfirmBox($this, Yii::t('app', '入力中のデータが保存されませんが、よろしいですか？'));
TextareaAutosizeAsset::register($this);

$this->title                   = Yii::t('app', '掲載のお問い合わせ');
$this->params['breadcrumbs'][] = $this->title;
$this->params['bodyId']        = 'inquiry';
//応募フロー
$currentClass['index'] = 'class="current"';

$script = <<<JS
$(function() {
   $("#policy-inquiry").click(function(e) {
       window.open($(this).attr('data-url'), '_blank', 'width=800, height=800, scrollbars=yes');
   });
});
JS;
$this->registerJs($script);
?>
<div class="container subcontainer">
    <div class="row">
        <!--▼ここからコンテンツスタート▼-->
        <div class="col-sm-12">

            <?= $this->render('_flow', ['currentClass' => $currentClass]) ?>

            <h2 class="mod-h4"><?= Yii::t('app', 'お問い合わせ情報をご入力ください') ?></h2>

            <?php
            //テーブルフォーム開始
            $kyujinForm = KyujinForm::begin(['id' => 'inquiry-form', 'action' => '/inquiry/confirm']);

            $kyujinForm->beginTable();
            ?>

            <?php
            foreach ((array)Yii::$app->functionItemSet->inquiry->items as $inquiryColumnName => $inquiryColumnSet) {
                /* @var $inquiryColumnSet \app\models\manage\InquiryColumnSet */
                switch ($inquiryColumnName) {
                    case 'company_name':
                    case 'post_name':
                    case 'tanto_name':
                    case 'tel_no':
                    case 'fax_no':
                    case 'job_type':
                    case 'postal_code':
                        echo $kyujinForm->row($inquiry,
                            $inquiryColumnName)->textInput(['placeholder' => $inquiryColumnSet->placeholder]);
                        break;
                    case 'address':
                        echo $this->render('_complete-address-from', [
                            'form'            => $kyujinForm,
                            'model'           => $inquiry,
                            'attribute'       => $inquiryColumnName,
                            'columnSet'       => $inquiryColumnSet,
                            'dependAttribute' => 'postal_code',
                        ]);
                        break;
                    case 'mail_address':
                        echo $this->render('/common/_complete-mail-domain-form', [
                            'form'       => $kyujinForm,
                            'model'      => $inquiry,
                            'columnName' => $inquiryColumnName,
                        ]);
                        break;
                    default:
                        echo $this->render('/common/_options-form', [
                            'kyujinForm'     => $kyujinForm,
                            'columnSetModel' => $inquiryColumnSet,
                            'model'          => $inquiry,
                        ]);
                        break;
                }
            };
            ?>
            <?php $kyujinForm->endTable(); ?>

            <p class="form-privacyLink text-center">
           <span class="privacyLink">
           <?= Html::a($policy->policy_name, 'javascript:void(0)',
               $options = ['id' => 'policy-inquiry', 'data-url' => Url::to(['/policy', 'policy_no' => $policy->policy_no])]) ?>
           <?= Yii::t('app', 'をご覧ください') ?>
           </span>

            </p>
            <div class="mod-box-center w60 w90-sp">
                <?= Html::submitButton(Yii::t('app', '上記保護方針に同意のうえお問い合わせする'), ['class' => 'mod-btn2']) ?>
            </div>
            <?php $kyujinForm->end(); ?>
        </div>
    </div>
</div>
