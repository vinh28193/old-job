<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\manage\AccessLogSearch;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\common\widget\FormattedDatePicker;
use proseeds\widgets\PopoverWidget;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $accessLogSearch app\models\manage\AccessLogSearch */

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
        width:9% !important;
        float:left;
        margin-right:1%;
        margin-top:8px;
    }
}
</style>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list'), 'enableClientValidation' => false]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">
            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $accessLogSearch->getAttributeLabel('accessPageId') ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeDropDownList(
                                $accessLogSearch,
                                'accessPageId',
                                AccessLogSearch::accessPageArray(),
                                ['prompt' => Yii::t('app', 'すべて'), 'class' => 'form-control jq-placeholder']
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= Html::activeLabel($accessLogSearch, 'jobNo'); ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeInput(
                                'text',
                                $accessLogSearch,
                                'jobNo',
                                ['placeholder' => '', 'class' => 'form-control']
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title col-popover-title">
                            <?= Html::activeLabel($accessLogSearch, 'access_url'); ?>
                        </div>
                        <div class="col-popover">
                            <?= PopoverWidget::widget(['dataContent' => Yii::t('app', 'アクセスしたページのみ候補文字に表示されます。')]) ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?php
                            echo Select2::widget([
                                'model' => $accessLogSearch,
                                'attribute' => 'access_url',
                                'initValueText' => $accessLogSearch->access_url ? $accessLogSearch->access_url : Yii::t('app', 'すべて'),
                                'options' => [
                                    'placeholder' => Yii::t('app', 'すべて'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 1,
                                    'language' => [
                                        'inputTooShort' => new JsExpression('function () {return "' . Yii::t('app', '1文字以上入力してください') . '";}'),
                                        'errorLoading' => new JsExpression('function () {return "' . Yii::t('app', '読み込み中…') . '";}'),
                                        'noResults' => new JsExpression('function () {return "' . Yii::t('app', '対象が見つかりません') . '";}'),
                                    ],
                                    'ajax' => [
                                        'url' => Url::to('/manage/secure/api/access-url-list-search', true),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                    ],
                                ],
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title col-popover-title">
                            <?= Html::activeLabel($accessLogSearch, 'access_user_agent'); ?>
                        </div>
                        <div class="col-popover" style="width:5%">
                            <?= PopoverWidget::widget(['dataContent' => Yii::t('app', 'アクセスしたページのみ候補文字に表示されます。')]) ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?php
                            echo $this->render('_auto-complete-form', [
                                'form' => $form,
                                'model' => $accessLogSearch,
                                'columnName' => 'access_user_agent',
                                'className' => 'auto-complete-access-user-agent',
                                'url' => Url::to('/manage/secure/api/access-user-agent-list-search', true),
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title col-popover-title">
                            <?= Html::activeLabel($accessLogSearch, 'access_referrer'); ?>
                        </div>
                        <div class="col-popover" style="width:5%">
                            <?= PopoverWidget::widget(['dataContent' => Yii::t('app', 'アクセスしたページのみ候補文字に表示されます。')]) ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?php
                            echo $this->render('_auto-complete-form', [
                                'form' => $form,
                                'model' => $accessLogSearch,
                                'columnName' => 'access_referrer',
                                'className' => 'auto-complete-access-referrer',
                                'url' => Url::to('/manage/secure/api/access-referrer-list-search', true),
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= Html::activeLabel($accessLogSearch, 'accessed_at'); ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <div id="jobmastersearch-startfrom-kvdate" class="input-group input-daterange">
                                <?= FormattedDatePicker::widget([
                                    'model' => $accessLogSearch,
                                    'attribute' => 'searchStartDate',
                                    'type' => FormattedDatePicker::TYPE_INPUT,
                                ]); ?>
                                <span class="input-group-addon kv-field-separator">~</span>
                                <?= FormattedDatePicker::widget([
                                    'model' => $accessLogSearch,
                                    'attribute' => 'searchEndDate',
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
                            <?= $accessLogSearch->getAttributeLabel('carrier_type') ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeRadioList(
                                $accessLogSearch,
                                'carrier_type',
                                AccessLogSearch::getCarrierTypeList(),
                                [
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
                'model' => $accessLogSearch,
            ]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>