<?php
use yii\bootstrap\Modal;
use yii\helpers\Html;
?>

<?php
if($jobMasterDisp->application_tel_1 != null || $jobMasterDisp->application_tel_2 != null){
    Modal::begin([
        'options' => ['class' => 'modal fade tel-Modal tel-Modal_'.$jobMasterDisp->job_no, 'aria-labelledby' => 'myModalLabel', 'aria-hidden' => 'true', ],
        'id' => 'tel-Modal',
        'header' => '<h4 class="mod-h2" id="myModalLabel">'.Yii::t('app', '電話応募').'</h4>',
        'footer' => '<div class="btn-group">
                                    <div class="btn-group__right hide view-sp">
                                        <button class="mod-btn3" data-dismiss="modal" type="button">'.Yii::t('app', '閉じる').'</button>
                                    </div>
                                    <div class="btn-group__right">
                                        <button id="area_set" class="mod-btn3 btn-group__right hide-sp" data-dismiss="modal" type="button">'.Yii::t('app', '閉じる').'</button>
                                    </div>
                                </div>',
    ]);

    if($jobMasterDisp->application_tel_1 != null){
        ?>
        <div class="btn-group">
            <div class="btn-group__center">
                <span><?= Yii::t('app', '電話番号1') ?></span>
                <a href="<?= 'tel:' . str_replace('-', '', Html::encode($jobMasterDisp->application_tel_1)) ?>" class="mod-btn3">
                    <i class="fa fa-phone-square"></i><?= Html::encode($jobMasterDisp->application_tel_1) ?>
                </a>
            </div>
        </div>
    <?php }

    if($jobMasterDisp->application_tel_2 != null){
        ?>
        <div class="btn-group">
            <div class="btn-group__center">
                <span><?= Yii::t('app', '電話番号2') ?></span>
                <a href="<?= 'tel:' . str_replace('-', '', Html::encode($jobMasterDisp->application_tel_2)) ?>" class="mod-btn3">
                    <i class="fa fa-phone-square"></i><?= Html::encode($jobMasterDisp->application_tel_2) ?>
                </a>
            </div>
        </div>
    <?php } ?>
    <?php
    Modal::end();
} ?>