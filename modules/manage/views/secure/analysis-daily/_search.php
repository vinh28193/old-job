<?php

use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\manage\AccessLogDailySearch;
use app\models\manage\AccessLog;

use app\modules\manage\models\Manager;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\depdrop\DepDrop;
use app\models\manage\searchkey\Pref;
use proseeds\widgets\PopoverWidget;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $accessLogDailySearch app\models\manage\AccessLogDailySearch */

$listClass = ['class' => 'form-control select select-simple max-w inline'];
$identity = Yii::$app->user->identity;
?>
<style>
    .col-popover-title {
        width:26.33333333%;
        padding:0px;
    }
    .col-popover {
        width:6%;
        float:left;
        margin-right:1%;
        margin-top:8px;
    }
@media only screen and (max-width: 732px) {
    .col-popover-title {
        width:23.33333333%;
        padding:0px;
    }
    .col-popover {
        width:9%;
        float:left;
        margin-right:1%;
        margin-top:8px;
    }
}
</style>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">
            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title col-popover-title" style="margin-top:0px;">
                            <?= $accessLogDailySearch->getAttributeLabel('prefId') ?>
                        </div>
                        <div class="col-popover">
                            <?= PopoverWidget::widget(['dataContent' => Yii::t('app', '求人詳細_PC、求人詳細_スマホ、応募完了_PC、応募完了_スマホのアクセス数に反映されます。')]) ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?=Html::activeDropDownList(
                                $accessLogDailySearch,
                                'prefId',
                                ['' => Yii::t('app', '都道府県')] + Pref::getPrefId(true, true), // todo やり方考えよう
                                ['class' => 'form-control jq-placeholder']
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title col-popover-title">
                            <?= Html::activeLabel($accessLogDailySearch, 'jobNo'); ?>
                        </div>
                        <div class="col-popover">
                            <?= PopoverWidget::widget(['dataContent' => Yii::t('app', '求人詳細_PC、求人詳細_スマホ、応募完了_PC、応募完了_スマホのアクセス数に反映されます。')]) ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeInput(
                                'text',
                                $accessLogDailySearch,
                                'jobNo',
                                ['placeholder' => '', 'class' => 'form-control']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->render('/secure/common/_dep-drop', [
                'model' => $accessLogDailySearch,
                'corpAttribute' => 'corpMasterId',
                'clientAttribute' => 'clientMasterId',
                'disableSecondRow' => true,
                'popoverLabel' => Yii::t('app', '求人詳細_PC、求人詳細_スマホ、応募完了_PC、応募完了_スマホのアクセス数に反映されます。'),
            ]) ?>

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $accessLogDailySearch->getAttributeLabel('accessMonth') ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeRadioList(
                                $accessLogDailySearch,
                                'accessMonth',
                                AccessLogDailySearch::getAccessMonthList(),
                                [
                                    'value' => $accessLogDailySearch['accessMonth'] ? $accessLogDailySearch['accessMonth'] : 1,
                                    'options' => 'col-xs-8 col-sm-8 col-md-7 right',
                                    'itemOptions' => [
                                        'labelOptions' => ['class' => 'radio-inline radio inline'],
                                        'data-toggle' => 'radio',
                                    ],
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->render('/secure/common/_search-buttons.php', [
                'model' => $accessLogDailySearch,
            ]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>