<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $mailAddress */
/* @var $this \yii\web\View */

$this->title                   = Yii::t('app', 'パスワード再設定完了');
$this->params['breadcrumbs'][] = ['label' => Html::encode('パスワード再設定完了')];
$this->params['bodyId']        = 'pass-entry-complete';
?>
<div class="container subcontainer">
    <div class="row">
        <!-- Main Contents =============================================== -->
        <!-- Container =========================== -->
        <?php $form = ActiveForm::begin(['method' => 'post', 'action' => Url::to('/manage/login'), 'id' => 'loginform']); ?>

        <div class="col-sm-12">

            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= Yii::t('app', 'パスワード再設定完了') ?></h1>
                <div class="mod-subbox">
                    <p><?= Yii::t('app', 'パスワードの再設定が完了しました。パスワードはこまめに変更されることをおすすめします。<br />
                    こちらから管理画面へとお進みください。') ?></p>

                    <div class="btn-group">
                        <div class=" btn-group__center">
                            <?= Html::a(Yii::t('app', '管理画面TOPへ戻る'), ['entry-complete#'],
                                ['class' => 'mod-btn2', 'onclick' => 'doSubmit();']) ?>
                        </div>
                    </div>

                </div>
            </div>


        </div><!-- / .col-sm-12 -->

        <?= Html::hiddenInput('loginId', $loginId) ?>
        <?= Html::hiddenInput('password', $password) ?>
        <?= Html::hiddenInput('doLogin', 'true') ?>
        <?php ActiveForm::end(); ?>
        <!-- / Main Contents =============================================== -->
    </div>
</div>
<script language="JavaScript" type="text/JavaScript">
    <!--
    function doSubmit() {
        document.getElementById('loginform').submit();
    }
    -->
</script>