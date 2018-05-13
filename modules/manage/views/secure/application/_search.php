<?php

use app\common\widget\FormattedDatePicker;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\manage\ApplicationMaster;
use app\models\manage\ApplicationStatus;
use app\models\manage\searchkey\Pref;

/* @var $this yii\web\View */
/* @var $applicationMasterSearch \app\models\manage\ApplicationMasterSearch */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs('
			var flg = "close";
			$("#hide_btn2,#hide_btn1").click(function(){
				$("#search-condition").slideToggle(200);
				if(flg == "close"){
					$("#hide_btn2,#hide_btn1").text("' . Yii::t('app', '詳しい条件を折りたたむ') . '");
					flg = "open";
				}else{
					$("#hide_btn2,#hide_btn1").text("' . Yii::t('app', 'さらに詳しい条件を指定する') . '");
					flg = "close";
				}
			});
');

// todo 一時的な処置。表示条件制御が強引すぎるので、管理画面検索フォームを生成するようなwidgetを作ってすっきり書くべき
$extraSearchForms = [];
foreach (['pref_id', 'sex', 'birth_date'] as $key => $item) {
    if (in_array($item, Yii::$app->functionItemSet->application->attributes)) {
        $input = '';
        switch ($item) {
            case 'pref_id':
                $input = Html::activeDropDownList(
                    $applicationMasterSearch,
                    'pref_id',
                    ['' => '都道府県'] + Pref::getPrefId(true, true), // todo やり方考えよう
                    ['class' => 'form-control jq-placeholder']
                );
                break;
            case 'sex':
                $input = Html::activeRadioList(
                    $applicationMasterSearch,
                    'sex',
                    [0 => Yii::t('app', '男性'), 1 => Yii::t('app', '女性')] // todo やり方考えよう
                );
                break;
            case 'birth_date':
                $year = Html::activeDropDownList(
                    $applicationMasterSearch,
                    'birthDateYear',
                    ['all' => Yii::t('app', '年')] + ApplicationMaster::getBirthYearList(), // todo やり方考えよう
                    ['class' => 'form-control jq-placeholder',]
                );
                $month = Html::activeDropDownList(
                    $applicationMasterSearch,
                    'birthDateMonth',
                    ['all' => Yii::t('app', '月')] + ApplicationMaster::getBirthMonthList(), // todo やり方考えよう
                    ['class' => 'form-control jq-placeholder',]
                );
                $day = Html::activeDropDownList(
                    $applicationMasterSearch,
                    'birthDateDay',
                    ['all' => Yii::t('app', '日')] + ApplicationMaster::getBirthDayList(), // todo やり方考えよう
                    ['class' => 'form-control jq-placeholder',]
                );

                $input =<<<HTML
<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 left">
        {$year}
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 center">
        {$month}
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 right">
        {$day}
    </div>
    </div>
HTML;
                break;
            default:
                break;
        }
        $extraSearchForms[] = [
            'label' => Html::activeLabel($applicationMasterSearch, $item),
            'input' => $input,
        ];
    }
}
?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
<div class="panel panel-default search-box arrow">
    <div class="container">

        <div class="row">
            <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
                <div class="row">
                    <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4 title">
                                <?= Html::activeLabel($applicationMasterSearch, 'jobNo'); ?>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8 right">
                                <?= Html::activeInput(
                                    'text',
                                    $applicationMasterSearch,
                                    'jobNo',
                                    ['placeholder' => '', 'class' => 'form-control']
                                ) ?>
                            </div>
                        </div>
                    </div>
                    <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4 title_search">
                                <?= Html::activeDropDownList(
                                    $applicationMasterSearch,
                                    'searchItem',
                                    ['all' => 'すべて'] + Yii::$app->functionItemSet->application->searchMenuAttributeLabels,
                                    ['class' => 'form-control select select-info max-w', 'data-toggle' => 'select']
                                ) ?>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8 right">
                                <?= Html::activeTextInput(
                                    $applicationMasterSearch,
                                    'searchText',
                                    ['placeholder' => 'キーワードを入力', 'class' => 'form-control jq-placeholder',]
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?= $this->render('/secure/common/_dep-drop', [
            'model' => $applicationMasterSearch,
            'corpAttribute' => 'corpMasterId',
            'clientAttribute' => 'clientMasterId',
            'clientChargePlanAttribute' => 'clientChargePlanId',
            'theOtherLabel' => Html::activeLabel($applicationMasterSearch, 'application_status_id'),
            'theOtherInput' => Html::activeDropDownList(
                $applicationMasterSearch,
                'application_status_id',
                ApplicationStatus::getDropDownList(Yii::t('app', 'すべて')),
                ['class' => 'form-control jq-placeholder']
            ),
        ]) ?>
        <div class="row">
            <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 title">
                        <?= Html::activeLabel($applicationMasterSearch, 'created_at'); ?>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8 right">
                        <div id="jobmastersearch-startfrom-kvdate" class="input-group input-daterange">
                            <?= FormattedDatePicker::widget([
                                'model' => $applicationMasterSearch,
                                'attribute' => 'searchStartDate',
                                'type' => FormattedDatePicker::TYPE_INPUT,
                            ]); ?>
                            <span class="input-group-addon kv-field-separator">~</span>
                            <?= FormattedDatePicker::widget([
                                'model' => $applicationMasterSearch,
                                'attribute' => 'searchEndDate',
                                'type' => FormattedDatePicker::TYPE_INPUT,
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 title">
                        <?= Html::activeLabel($applicationMasterSearch, 'isJobDeleted'); ?>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8 right">
                        <?= Html::activeRadioList(
                            $applicationMasterSearch,
                            'isJobDeleted',
                            [0 => Yii::t('app', '原稿あり'), 1 => Yii::t('app', '原稿削除済み')]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($extraSearchForms): ?>
            <div id="hide_box" class="collapse" aria-expanded="false" style="height: 0px;">
                <p class="text-center mgt20 mgb20">
                    <a id="hide_btn1" data-toggle="collapse" href="#hide_box" aria-expanded="false"
                       aria-controls="hide_box" class="collapsed">
                        <?= Yii::t('app', 'さらに詳しい条件を指定する') ?>
                    </a>
                </p>
                <div class="row dot-line">
                    <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                        <?php if (isset($extraSearchForms[0])): ?>
                            <div class="row">
                                <div class="col-xs-4 col-sm-4 col-md-4 title">
                                    <?= $extraSearchForms[0]['label']; ?>
                                </div>
                                <div class="col-xs-8 col-sm-8 col-md-8 right">
                                    <?= $extraSearchForms[0]['input'] ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                        <?php if (isset($extraSearchForms[1])): ?>
                            <div class="row">
                                <div class="col-xs-4 col-sm-4 col-md-4 title">
                                    <?= $extraSearchForms[1]['label']; ?>
                                </div>
                                <div class="col-xs-8 col-sm-8 col-md-8 right">
                                    <?= $extraSearchForms[1]['input'] ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                        <?php if (isset($extraSearchForms[2])): ?>
                            <div class="row">
                                <div class="col-xs-4 col-sm-4 col-md-4 title">
                                    <?= $extraSearchForms[2]['label']; ?>
                                </div>
                                <div class="col-xs-8 col-sm-8 col-md-8 right">
                                    <?= $extraSearchForms[2]['input'] ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    </div>
                </div>
            </div>
            <div class="row">
                <p class="text-center mgt20 mgb20">
                    <a id="hide_btn2" data-toggle="collapse" href="#hide_box" aria-expanded="false"
                       aria-controls="hide_box">
                        <?= Yii::t('app', 'さらに詳しい条件を指定する') ?>
                    </a>
                </p>
            </div>
        <?php endif; ?>
        <?= $this->render('/secure/common/_search-buttons.php', [
            'model' => $applicationMasterSearch,
        ]); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
