<?php
use app\models\manage\NameMaster;
use yii\helpers\Url;
use yii\helpers\Html;
use app\common\widget\KeepWidget;

/* @var app\models\JobMasterDisp $jobMasterDisp */
/* @var bool $isPreview プレビュー機能で表示しているか ボタンのリンクを無効にしている */
/** @var bool $isOtherButton */

$applicationName = NameMaster::getChangeName('応募');
?>
<!--応募ボタングループ-->
<div class="btn-group">
    <div class="btn-group__center">
        <?php
        //電話応募ボタン
        if ($jobMasterDisp->application_tel_1 != null || $jobMasterDisp->application_tel_2 != null) {
            echo Html::a(
                '<i class="fa fa-phone-square"></i> ' . Yii::t('app', '電話{application}する', ['application' => $applicationName]),
                (!$isPreview) ? 'javascript:void(0);' : '#',
                [
                    'class' => 'mod-btn7 btn-group__left',
                    'name' => 'tel-btn',
                    'data-toggle' => 'modal',
                    'data-target' => '.tel-Modal_' . $jobMasterDisp->job_no,
                ]
            );
        }

        //応募ボタン
        if ($jobMasterDisp->application_mail != null) {
            echo Html::a(
                '<span class="fa fa-paper-plane"></span> ' . Yii::t('app', '{application}する', ['application' => $applicationName]),
                (!$isPreview) ? Url::to(['/apply/index', 'job_no' => $jobMasterDisp->job_no]) : '#',
                [
                    'class' => 'mod-btn7 btn-group__right',
                    'name' => 'application-btn',
                ]
            );
        }
        ?>
    </div>
</div>

<?php if ($isOtherButton): ?>
    <div class="btn-group">
        <div class="btn-group__center">
            <a href="<?= (!$isPreview) ? '/kyujin/send-mobile/' . $jobMasterDisp->job_no : '#' ?>"
               class="mod-btn5 btn-group__left"><?= Yii::t('app', 'メールで転送する') ?></a>
            <?= KeepWidget::widget(['model' => $jobMasterDisp, 'options' => ['class' => 'mod-btn5 btn-group__right']]); ?>
        </div>
    </div>
<?php endif; ?>

