<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\modules\manage\models\ManageAuth */

use app\models\manage\Policy;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$css = <<<CSS
.form-signin-type{
  padding:10px 10px;
  margin-bottom: 10px;
  height:auto;
}
CSS;
$this->registerCss($css);

$this->title = Yii::t('app', 'JobMaker ログイン');
$this->params['breadcrumbs'][] = $this->title;
$policy = Policy::findOne(['policy_no' => Policy::ADMIN_POLICY_NO, 'valid_chk' => Policy::VALID]);
?>
<div class="row vertical-offset-100">
    <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row-fluid logo-box">
                    <img src="/systemdata/pict/manage/logo.png?public=1" width="180" alt="logo"/>
                </div>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'options' => [
                        'accept-charset' => 'UTF-8',
                        'class' => 'form-signin form-horizontal',
                        'role' => 'form',
                    ],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-lg-12\">{input}\n{error}</div>",
                        'labelOptions' => ['class' => 'col-lg-1 control-label'],
                    ],
                ]); ?>
                <fieldset>
                    <label class="panel-login">
                        <div class="login_result"><?= $message ?></div>
                    </label>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <?= $form->field($model, 'loginId')->textInput(['class' => 'form-control center-block form-signin-type', 'placeholder' => 'ログインID(半角英数字)'])->label(false) ?>
                        </div>
                        <div class="col-sm-12">
                            <?= $form->field($model, 'password')->passwordInput(['class' => 'form-control form-signin-type', 'placeholder' => 'パスワード'])->label(false) ?>
                        </div>
                    </div>
                    <!--<label for="keep" class="checkbox-inline checkbox inline adminlogin-keep-box">
                    <?= Html::checkbox('rememberMe', false, ['class' => '', 'id' => 'keep']); ?>
                        ログイン状態を保持する
                    </label>-->
                    <p>
                        <?= Html::submitButton('ログイン', ['class' => 'btn btn-lg btn-block btn-primary mgb10', 'id' => 'login', 'name' => 'login-button']) ?>
                    </p>
                    <p><span class="glyphicon glyphicon-chevron-right"></span> <a href="/pass/apply?flg=admin"><?= Yii::t('app', 'パスワードを忘れた方はこちら') ?></a></p>
                    <?php if($policy !== null): ?>
                        <p><span class="glyphicon glyphicon-chevron-right"></span> <?= Html::a($policy->policy_name, 'javascript:void(0)', $options = ['id' => 'policy', 'data-url' => Url::to(['/policy/index', 'policy_no' => $policy->policy_no])]) ?>をご覧ください</p>
                    <?php endif; ?>
                </fieldset>
                <?= Html::hiddenInput('doLogin', 'true') ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$css = <<<CSS
   .help-block{
       display :none;
   }
CSS;
$this->registerCss($css);
?>
<?php
$script = <<<JS
$(function() {
    $("#policy").click(function(e) {
        window.open($(this).attr('data-url'), '_blank', 'width=800, height=800, scrollbars=yes');
    });
});
JS;
$this->registerJs($script);
?>
