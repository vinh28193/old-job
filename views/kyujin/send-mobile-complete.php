<?php

use yii\helpers\Url;
use app\models\manage\NameMaster;
use app\models\ToolMaster;

/* @var $jobMasterDisp app\models\JobMasterDisp */
/* @var $this \yii\web\View */

Yii::$app->site->toolNo = ToolMaster::TOOLNO_MAP['sendMobileCompleted'];
Yii::$app->site->jobNo  = $job_no;

$kyujinName                    = NameMaster::getChangeName('求人');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'メール転送完了')];
$this->params['bodyId']        = 'send-mobile-complete';

$this->params['h1'] = true;
?>
<div class="container subcontainer">
    <div class="row">
        <!-- Main Contents =============================================== -->
        <!-- Container =========================== -->
        <div class="col-sm-12">
            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= Yii::t('app', 'メール転送完了') ?></h1>
                <div class="mod-subbox">
                    <h2><?= Yii::t('app', 'メールの送信が完了しました。') ?></h2>
                    <p><?= Yii::t('app', 'ご入力頂いたメールアドレス宛てにメールを送信致しました。<br>
                    しばらく経ってもメールが届かない場合には、お手数をお掛けしますが、<br>
                    再度メールアドレスを入力して、送信をお願い致します。') ?></p>

                    <div class="mod-box-center w40">
                        <div class="btn_box center">
                            <p class="mod-btn2"><a class="btn_gray" href="<?= Url::toRoute(['/kyujin/' . $job_no]) ?>"><?= Yii::t('app',
                                        '{kyujin}詳細へ', ['kyujin' => $kyujinName]) ?></a></p>
                        </div>
                    </div>

                </div>
            </div>
        </div><!-- / .col-sm-12 -->
        <!-- / Main Contents =============================================== -->
    </div>
</div>
