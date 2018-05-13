<?php

use app\assets\ApplyAsset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use app\models\ToolMaster;

/* @var \app\models\manage\ApplicationMaster $applicationModel */
/* @var app\models\JobMasterDisp $jobMaster */

ApplyAsset::register($this);

Yii::$app->site->toolNo = ToolMaster::TOOLNO_MAP['applicationCompleted'];
Yii::$app->site->jobNo = $job_no;

$title = Yii::t('app', '応募完了');
$this->params['breadcrumbs'][] = $title;
$currentClass['complete'] = true;
$this->params['bodyId'] = 'apply-complete';

$this->params['h1'] = true;

$historyUrl = Url::to('mypage/entry-history', true);

// no index metaタグを追加
$this->registerMetaTag([
    'name' => 'robots',
    'content' => 'noindex',
]);
?>
<div class="container subcontainer">
    <div class="row">
        <!--▼ここからコンテンツスタート▼-->
        <div class="col-sm-12">

            <?= $this->render('_flow', ['currentClass' => $currentClass]) ?>

            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= Yii::t('app', '応募が完了しました。') ?></h1>
                <div class="mod-subbox">
                    <h1 class="mod-subbox__thanks"><?= Yii::t('app', 'ご応募ありがとうございました。') ?></h1>
                    <div class="mod-subbox__thanks_id">
                        <h4><?php echo Yii::$app->functionItemSet->application->items['application_no']->label . '：' . Yii::$app->session->getFlash('applicationNo') ?></h4>
                    </div>
                    <p class="txt"> <?= Yii::t('app', 'この度は本サービスにて掲載中の求人情報へご応募をいただきありがとうございます。') ?><br>
                        <?= Yii::t('app', '採用担当者より改めてご連絡させていただきますのでしばらくお待ち下さい。 尚、弊社が休業日の場合はご連絡が遅れる場合がございますのでご了承下さい。') ?>
                    </p>
                    <p>
                        <?= Yii::t('app', 'ご応募いただいた内容は以下ページにてご確認いただけます。') ?><br>
                        <?= Html::a($historyUrl, $historyUrl) ?><br>
                        <?= Yii::t('app', '閲覧時、上記の{applicationNo}と応募時に入力された情報が必要になります。',
                            ['applicationNo' => Yii::$app->functionItemSet->application->items['application_no']->label]) ?>
                    </p>
                    <p>
                        <?= Yii::t('app', '自動返信メールが届かない場合は、メールアドレスの入力間違いの可能性がございます。') ?><br>
                        <?= Yii::t('app', 'お手数ですが、メールアドレスをご確認の上、再度お問い合わせいただくかお電話にてお問合せください。') ?>
                    </p>

                    <div class="mod-box-center w60 w90-sp">
                        <p class="mod-btn2"><a href="/"><?= Yii::t('app', 'トップページへ戻る') ?></a></p>
                    </div>
                </div>
            </div>

        </div><!-- / .col-sm-12 -->
    </div>
</div>
