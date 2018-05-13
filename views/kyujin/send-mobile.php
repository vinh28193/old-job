<?php

use app\common\KyujinForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\manage\NameMaster;
use app\models\ToolMaster;
use yii\bootstrap\BootstrapPluginAsset;

/* @var $jobMasterDisp app\models\JobMasterDisp */
/* @var $jobShortItemDispList array */
/* @var $this \yii\web\View */

//フォームのアイコン非表示
//$this->registerCss('span.form-control-feedback{display: none;}');

BootstrapPluginAsset::register($this);
Yii::$app->site->toolNo    = ToolMaster::TOOLNO_MAP['sendMobileInput'];
Yii::$app->site->jobMaster = $jobMasterDisp;

$kyujinName = NameMaster::getChangeName('求人');
$jobName    = NameMaster::getChangeName('仕事');
if ($jobMasterDisp->prefNames) {
    $this->params['breadcrumbs'][] = ['label' => $jobMasterDisp->prefNames];
}
if ($jobMasterDisp->distNames) {
    $this->params['breadcrumbs'][] = ['label' => $jobMasterDisp->distNames];
}
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', '{corpNameDisp}の{kyujin}詳細', ['corpNameDisp' => $jobMasterDisp->corp_name_disp, 'kyujin' => $kyujinName]),
    'url'   => Url::to(['/kyujin/' . $jobMasterDisp->job_no]),
];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'メールで転送する')];
$this->params['bodyId']        = 'send-mobile';

$this->params['h1'] = true;
?>
<div class="container subcontainer">
    <div class="row">
        <!-- Main Contents =============================================== -->
        <!-- Container =========================== -->
        <div class="col-sm-12">

            <!--result_sub-->
            <div class="result_sub">
                <?= $this->render('/common/_job-short-item-disp',
                    ['model' => $jobMasterDisp, 'headerMessage' => Yii::t('app', 'メール転送する')]) ?>

                <div class="mod-subbox-wrap">

                    <h1 class="mod-h1"><?= Yii::t('app', '転送先情報入力フォーム') ?></h1>
                    <div class="mod-subbox">
                        <p><?= Yii::t('app', '上記の仕事情報のURLを送信します。下記フォームにメールアドレスを入力して「送信」ボタンを押してください。') ?></p>

                        <?php // フォーム
                        $tableForm = KyujinForm::begin(['action' => Url::toRoute(['kyujin/send-mail'])]);
                        $tableForm->beginTable();

                        echo $this->render('/common/_complete-mail-domain-form', [
                            'form'       => $tableForm,
                            'model'      => $jobMasterDisp,
                            'columnName' => 'mailAddress',
                        ]);
                        echo $tableForm->row($jobMasterDisp, 'message')->textarea(['class' => 'txtarea-default form-control', 'rows' => 5]);
                        echo Html::hiddenInput('job_no', $jobMasterDisp->job_no);

                        $tableForm->endTable()
                        ?>

                        <div class="btn-group">
                            <div class=" btn-group__center">
                                <button type="submit" class="mod-btn2"><?= Yii::t('app', '送信する') ?></button>
                            </div>
                        </div>
                        <?php $tableForm->end() ?>
                    </div>
                </div>
                <!-- /main -->
            </div>
            <!-- /sub -->


        </div><!-- / .col-sm-12 -->

        <!-- / Main Contents =============================================== -->
    </div>
</div>
