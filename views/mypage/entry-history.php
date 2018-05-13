<?php

use app\assets\ApplyAsset;
use app\models\manage\NameMaster;
use yii\bootstrap\Html;

/* @var $this \yii\web\View */
/* @var $apply \app\models\Apply */
/* @var $jobDispList array */
/* @var $jobMaster app\models\manage\JobMaster */

ApplyAsset::register($this);

$this->title                   = Yii::t('app', '{corpNameDisp}{applicationName}内容',
    ['corpNameDisp' => $jobMaster->corp_name_disp, 'applicationName' => NameMaster::getChangeName('応募')]);
$this->params['breadcrumbs'][] = $this->title;
$this->params['bodyId']        = 'entry-history';

?>
<div class="container subcontainer">
    <div class="row">
        <!--▼ここからコンテンツスタート▼-->
        <div class="col-sm-12">

            <h1 class="mod-h2"><?= Yii::t('app', '応募内容') ?></h1>
            <div class="mod-h3"><?= Yii::t('app', 'ご自身で送信された応募内容です。') ?></div>

            <div class="well">
                <?= Yii::t('app', '応募日:') . Yii::$app->formatter->asDate($apply->created_at) ?>
                <br>
                <?= Html::a(Yii::t('app', '応募した求人情報'), '/kyujin/' . $apply->jobMaster->job_no) ?>
            </div>

            <?= $this->render('/common/_job-short-item-disp', ['model' => $jobMaster, 'headerMessage' => Yii::t('app', '応募先情報')]) ?>

            <?= $this->render('/common/_apply-detail', ['model' => $apply, 'headerMessage' => Yii::t('app', '応募情報')]) ?>

        </div>
    </div>
</div>