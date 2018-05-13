<?php

use yii\helpers\Html;

/* @var $mailAddress */
/* @var $this \yii\web\View */

$this->title                   = Yii::t('app', 'パスワード再設定申請完了');
$this->params['breadcrumbs'][] = ['label' => Html::encode('パスワード再設定申請完了')];
$this->params['bodyId']        = 'pass-apply-complete';
?>
<div class="container subcontainer">
    <div class="row">
        <!-- Main Contents =============================================== -->
        <!-- Container =========================== -->
        <div class="col-sm-12">

            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= Yii::t('app', 'パスワード再設定申請完了') ?></h1>
                <div class="mod-subbox">
                    <p><?= Yii::t('app', '「{MAIL_ADDRESS}」宛に、ログインIDの確認および、パスワード再設定用メールを送信しました。<br />
                    <br />
                    メールの本文に記載されているパスワード再設定用URLをクリックすると、パスワードの再設定を行うページが表示されます。<br />
                    URLをクリックしても同ページが表示されないときはURLをコピーし、ご利用のウェブブラウザーのアドレス入力欄に貼り付けてお試しください。<br />
                    <br />
                    ★注意事項<br />
                    ・状況により、パスワード再設定用メールが届くまでに多少の時間を要する場合があります。あらかじめご了承ください。<br />
                    ・パスワード再設定用メールに記載されたパスワード再設定用URLは、送信より2時間経過すると無効になりますので、時間内にクリックしてパスワード再設定を完了させてください。',
                            ['MAIL_ADDRESS' => $mailAddress]) ?></p>
                </div>
            </div>

        </div><!-- / .col-sm-12 -->
        <!-- / Main Contents =============================================== -->
    </div>
</div>