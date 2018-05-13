<?php

use app\common\KyujinForm;
use yii\helpers\Html;

/* @var $jobMasterDisp app\models\JobMasterDisp */
/* @var $this \yii\web\View */
/* @var $flg bool */

$this->title                   = Yii::t('app', 'パスワード再設定申請');
$this->params['breadcrumbs'][] = ['label' => Html::encode('パスワード再設定申請')];
$this->params['bodyId']        = 'pass-apply';
?>
<div class="container subcontainer">
    <div class="row">
        <!-- Main Contents =============================================== -->
        <!-- Container =========================== -->
        <div class="col-sm-12">

            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= Yii::t('app', 'パスワード再設定申請') ?></h1>
                <div class="mod-subbox">
                    <p><?= Yii::t('app', '以下のメールアドレス入力欄に、登録しているメールアドレスをご入力の上、【送信する】をクリックしてください。<br />
                        メールアドレスの確認後、そのメールアドレス宛に以下を記載したメールが自動送信されます。<br />') ?>
                        <?= Yii::t('app', '・ログインID') ?><br/>
                        <?= Yii::t('app', '・パスワード再設定用URL') ?></p>

                    <?= Yii::$app->session->getFlash('operationComment') ?>

                    <?php // フォーム
                    $tableForm = KyujinForm::begin(['action' => 'apply-complete']);
                    $tableForm->beginTable();

                    //メールアドレス
                    echo $this->render('/common/_complete-mail-domain-form', [
                        'form'       => $tableForm,
                        'model'      => $model,
                        'columnName' => 'mail_address',
                        'className'  => 'input-txt-large',
                    ]);

                    $tableForm->endTable();
                    ?>

                    <div class="btn-group">
                        <div class=" btn-group__center">
                            <?= Html::submitButton(Yii::t('app', '送信する'), ['class' => 'mod-btn2', 'name' => 'password-setting']) ?>
                        </div>
                    </div>

                    <?= Html::hiddenInput('flg', $flg) ?>

                    <?php $tableForm->end(); ?><!--.mod-form1-loginform-->
                </div>
            </div>
        </div><!-- / .container -->

        <!-- / Main Contents =============================================== -->
    </div>
</div>