<?php
use app\common\widget\FormattedDatePicker;
use app\models\manage\searchkey\Area;
use app\models\manage\WidgetData;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\manage\Widget;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\WidgetDataSearch */
/* @var $form yii\widgets\ActiveForm */

/* @var $areaComp \app\components\Area */
$areaComp = Yii::$app->area;
?>


<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list'), 'enableClientValidation' => false]); ?>
<div class="panel panel-default search-box arrow">
    <div class="container">
        <div class="row">
            <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
                <div class="row">
                    <?= $form->field($searchModel, 'searchItem', [
                        'template' => ' <div class="col-xs-4 col-sm-4 col-md-4">{input}</div>',
                    ])->dropDownList(['all' => Yii::t('app', 'すべて')] + $searchModel->getSearchItemArray(), [
                        'class' => 'form-control select select-info max-w inline',
                        'data-toggle' => 'select',
                    ]) ?>
                    <?= $form->field($searchModel, 'searchText', [
                        'template' => ' <div class="col-xs-8 col-sm-8 col-md-8 right">{input}</div>',
                    ])->textInput(['class' => 'form-control jq-placeholder inline', 'placeholder' => Yii::t('app', 'キーワードを入力')]) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                <div class="row">
                    <?= $form->field($searchModel, 'areaId', [
                        'template' => '<div class="col-xs-4 col-sm-4 col-md-4 title">{label}</div><div class="col-xs-8 col-sm-8 col-md-8 right">{input}</div>',
                    ])->dropDownList($areaComp->listArray, ['class' => 'form-control jq-placeholder inline', 'prompt' => Yii::t('app', 'すべて')]) ?>
                </div>
            </div>
            <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                <div class="row">
                    <?= $form->field($searchModel, 'widget_id', [
                        'template' => '<div class="col-xs-4 col-sm-4 col-md-4 title">{label}</div><div class="col-xs-8 col-sm-8 col-md-8 right">{input}</div>',
                    ])->dropDownList(Widget::getDropDownArray(), ['class' => 'form-control jq-placeholder inline', 'prompt' => Yii::t('app', 'すべて')]) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 title">
                        <?= Html::activeLabel($searchModel, 'disp_start_date') ?>
                    </div>
                    <div class="col-xs-8 col-sm-8 col-md-8 right">
                        <div id="jobmastersearch-startfrom-kvdate" class="input-group input-daterange">
                            <?= FormattedDatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'startFrom',
                                'type' => FormattedDatePicker::TYPE_INPUT,
                            ]); ?>
                            <span class="input-group-addon kv-field-separator">~</span>
                            <?= FormattedDatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'startTo',
                                'type' => FormattedDatePicker::TYPE_INPUT,
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                <div class="row">
                    <?= $form->field($searchModel, 'valid_chk', [
                        'template' => '<div class="col-xs-4 col-sm-4 col-md-4 title">{label}</div><div class="col-xs-8 col-sm-8 col-md-8 right">{input}</div>',
                    ])->radioList(WidgetData::validChkArray()) ?>
                </div>
            </div>
        </div>
        <?= $this->render('/secure/common/_search-buttons.php', [
            'model' => $searchModel,
        ]); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
