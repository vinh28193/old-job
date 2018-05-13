<?php

use app\common\widget\FormattedDatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\manage\JobMasterSearch;
use app\models\manage\JobReviewStatus;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $jobMasterSearch JobMasterSearch */

//$this->registerJs('$(document).ready(function(){
//                        var flg = "close";
//                        $("#hide_btn2,#hide_btn1").click(function(){
//                            $("#search-condition").slideToggle(200);
//                            if(flg == "close"){
//                                $("#hide_btn2,#hide_btn1").text("詳しい条件を折りたたむ");
//                                flg = "open";
//                            }else{
//                                $("#hide_btn2,#hide_btn1").text("さらに詳しい条件を指定する");
//                                flg = "close";
//                            }
//                        });
//                    });'
//        , View::POS_END, 'my-options');

$listClass = ['class' => 'form-control select select-simple max-w inline'];

?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">
            <div class="row">
                <div class="search-inbox col-xs-4 col-sm-4 col-md-4">
                    <?= Html::activeDropDownList(
                        $jobMasterSearch,
                        'searchItem',
                        ['all' => Yii::t('app', 'すべて')] + Yii::$app->functionItemSet->job->searchMenuAttributeLabels,
                        ['class' => 'form-control select select-info max-w inline', 'data-toggle' => 'select']
                    ) ?>
                </div>
                <div class="search-inbox col-xs-8 col-sm-8 col-md-8 right">
                    <?= Html::activeTextInput($jobMasterSearch, 'searchText', [
                        'placeholder' => Yii::t('app', 'キーワードを入力'),
                        'class' => 'form-control jq-placeholder inline',
                    ]) ?>
                </div>
            </div>
            <?= $this->render('/secure/common/_dep-drop', [
                'model' => $jobMasterSearch,
                'corpAttribute' => 'corpMasterId',
                'clientAttribute' => 'client_master_id',
                'clientChargePlanAttribute' => 'client_charge_plan_id',
                'theOtherLabel' => Html::activeLabel($jobMasterSearch, 'isDisplay'),
                'theOtherInput' => Html::activeDropDownList($jobMasterSearch, 'isDisplay', JobMasterSearch::getDispStatusList(), $listClass),
            ]) ?>

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= Html::activeLabel($jobMasterSearch, 'disp_start_date'); ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <div id="jobmastersearch-startfrom-kvdate" class="input-group input-daterange">
                                <?= FormattedDatePicker::widget([
                                    'model' => $jobMasterSearch,
                                    'attribute' => 'startFrom',
                                    'type' => FormattedDatePicker::TYPE_INPUT,
                                ]); ?>
                                <span class="input-group-addon kv-field-separator">~</span>
                                <?= FormattedDatePicker::widget([
                                    'model' => $jobMasterSearch,
                                    'attribute' => 'startTo',
                                    'type' => FormattedDatePicker::TYPE_INPUT,
                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= Html::activeLabel($jobMasterSearch, 'disp_end_date'); ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <div id="jobmastersearch-startfrom-kvdate" class="input-group input-daterange">
                                <?= FormattedDatePicker::widget([
                                    'model' => $jobMasterSearch,
                                    'attribute' => 'endFrom',
                                    'type' => FormattedDatePicker::TYPE_INPUT,
                                ]); ?>
                                <span class="input-group-addon kv-field-separator">~</span>
                                <?= FormattedDatePicker::widget([
                                    'model' => $jobMasterSearch,
                                    'attribute' => 'endTo',
                                    'type' => FormattedDatePicker::TYPE_INPUT,
                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= Html::activeLabel($jobMasterSearch, 'valid_chk'); ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeRadioList(
                                $jobMasterSearch,
                                'valid_chk',
                                [1 => Yii::t('app', '公開'), 0 => Yii::t('app', '非公開')]
                            ) ?>
                        </div>
                    </div>
                </div>
                <?php if (Yii::$app->tenant->tenant->review_use) : ?>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $jobMasterSearch->getAttributeLabel('job_review_status_id') ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeDropDownList(
                                $jobMasterSearch,
                                'job_review_status_id',
                                JobReviewStatus::reviewStatuses(true),
                                ['class' => 'form-control select select-simple max-w inline']
                            ) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?= $this->render('/secure/common/_search-buttons.php', [
                'model' => $jobMasterSearch,
            ]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>