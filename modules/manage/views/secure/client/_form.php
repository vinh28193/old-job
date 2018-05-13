<?php

use app\common\widget\FormattedDatePicker;
use app\models\manage\BaseColumnSet;
use app\models\manage\ClientChargePlan;
use app\modules\manage\models\Manager;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\manage\ClientMaster;
use proseeds\widgets\TableForm;
use proseeds\assets\BootBoxAsset;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\manage\ClientMaster */

/** @var Manager $identity */
$identity = Yii::$app->user->identity;

BootBoxAsset::confirmBeforeSubmit($this, $model->isNewRecord ? Yii::t("app", "掲載企業情報を登録してもよろしいですか？") : Yii::t("app", "掲載企業情報を変更してもよろしいですか？"));
$defaultItems = Yii::$app->functionItemSet->client->defaultItems;
ArrayHelper::remove($defaultItems, 'client_no');

// todo 汚いのでも少しスマートに
$planDisplayJs = <<<JS
$(".plan_name").each(function() {
    if ($(this).prop("checked")) {
        $(this).closest("div").next().show();
    } else {
        $(this).closest("div").next().hide();
    }
});

$(":radio:checked").each(function() {
    if ($(this).val() == 0) {
        $(this).closest(".cpp_in").find(".limitNum").parent("div").hide();
    }
});

$(".plan_name").on("change", function() {
    if ($(this).prop("checked")) {
        $(this).closest("div").next().show();
    } else {
        $(this).closest("div").next().hide();
    }
});

$(".hasLimit").on("click", function() {
    if ($(this).val() == 1) {
        $(this).closest(".cpp_in").find(".limitNum").parent("div").show();
    } else {
        $(this).closest(".cpp_in").find(".limitNum").parent("div").hide();
    }
});
JS;
$this->registerJs($planDisplayJs);

$syncPlansJs = <<<JS
$(".field-clientmaster-clientchargeplan").find(":checkbox").on("click", function () {
    $("#form").yiiActiveForm("validateAttribute", "clientmaster-clientchargeplan");
    $(".field-clientmaster-clientchargeplan").find("input").each(function() {
        $("#form").yiiActiveForm("validateAttribute", this.id);
    });
});
JS;
$this->registerJs($syncPlansJs);
?>

<div class="corp-master-form">
    <?php
    $tableForm = TableForm::begin([
        'id' => 'form',
        'options' => ['enctype' => 'multipart/form-data'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validationUrl' => Url::to(['ajax-validation', 'id' => $model->id]),
    ]);
    $fieldOptions = ['tableHeaderOptions' => ['class' => 'm-column'],];
    $tableForm->beginTable();

    foreach ($defaultItems as $defaultItem) {
        /** @var \app\models\manage\ClientColumnSet $defaultItem */
        switch ($defaultItem->column_name) {
            case 'corp_master_id':
                switch ($identity->myRole) {
                    case Manager::OWNER_ADMIN:
                        echo $tableForm->row($model, 'corp_master_id')->widget(Select2::className(), [
                            'model' => $model,
                            'attribute' => 'corp_master_id',
                            'initValueText' => isset($model->corpMaster) ? $model->corpMaster->corp_name: 'すべて',
                            'options' => [
                                'placeholder' => Yii::t('app', 'すべて'),
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 1,
                                'language' => ['inputTooShort' => new JsExpression('function () {return "1文字以上入力してください";}'),],
                                'ajax' => [
                                    'url' => Url::to('corp-list'),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                ],
                            ],
                        ]);
                        break;
                    case Manager::CORP_ADMIN:
                        // todo なんとかしよう
                        echo '<tr id="form-corp_master_id-tr"><th class="w30">'
                            . Html::activeLabel($model, 'corp_master_id')
                            . Html::tag('span', Yii::t('app', '必須'), ['class' => 'label label-warning fr'])
                            . '</th><td>' . $identity->corpMaster->corp_name . '</div></div></td></tr>';
                        break;
                    default :
                        break;
                }
                break;
            default:
                echo $tableForm->row($model, $defaultItem->column_name, $fieldOptions)->textInput();
                break;
        }
    }

    //申込みプラン
    echo $tableForm->field($model, 'clientChargePlan', $fieldOptions + ['template' => "{th}\n{label}\n{/th}\n{td}\n{input}\n{hint}\n{/td}"])->isRequired(true)->layout(function () use ($tableForm, $model, $fieldOptions) {
        echo $tableForm->field($model, 'clientChargePlan', ['enableAjaxValidation' => true, 'template' => '{input}{error}'])->hiddenInput();
        $indexedPlans = ClientChargePlan::indexedPlans();
        foreach ((array)$indexedPlans as $typeNo => $plans) {
            echo Html::label(ClientChargePlan::getChargeTypeName($typeNo));
            foreach ((array)$plans as $i => $plan):/** @var ClientChargePlan $plan */ ?>
                <div class="cpp">
                    <?= $tableForm->form($model->{'clientCharge' . $plan->id}, '[' . $plan->id . ']client_charge_plan_id', ['enableAjaxValidation' => true])
                        ->checkbox(['label' => $plan->plan_name, 'class' => 'plan_name', 'value' => $plan->id]); ?>
                    <div class="cpp_in row">
                        <div class="col-xs-12">
                            <div class="col-xs-6">
                                <?= $tableForm->form($model->{'clientCharge' . $plan->id}, '[' . $plan->id . ']limitType', ['enableAjaxValidation' => true])
                                    ->radio(['value' => 0, 'label' => Yii::t('app', '上限なし'), 'class' => 'hasLimit']) ?>
                            </div>
                            <div class="col-xs-6"></div>
                        </div>
                        <div class="col-xs-12">
                            <div class="col-xs-6">
                                <?= $tableForm->form($model->{'clientCharge' . $plan->id}, '[' . $plan->id . ']limitType', ['enableAjaxValidation' => true])
                                    ->radio(['value' => 1, 'label' => Yii::t('app', '枠を設定する'), 'class' => 'hasLimit']) ?>
                            </div>
                            <div class="col-xs-6">
                                <?= $tableForm->form($model->{'clientCharge' . $plan->id}, '[' . $plan->id . ']limit_num', ['enableAjaxValidation' => true])
                                    ->textInput(['class' => 'form-control limitNum', 'placeholder' => Yii::t('app', '件数を入力してください')]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach;
            echo '<br>';
        }
    });
    echo $tableForm->breakLine();

    //以下オプション
    foreach (Yii::$app->functionItemSet->client->optionItems as $optionItems) {
        switch ($optionItems->data_type) {
            case BaseColumnSet::DATA_TYPE_NUMBER:
            case BaseColumnSet::DATA_TYPE_URL:
            case BaseColumnSet::DATA_TYPE_MAIL:
                echo $tableForm->row($model, $optionItems->column_name, $fieldOptions)->textInput();
                break;
            case BaseColumnSet::DATA_TYPE_DROP_DOWN:
                echo $tableForm->row($model, $optionItems->column_name, $fieldOptions)->dropDownList(["" => Yii::t("app", "(未選択)")] + $optionItems->subsetList);
                break;
            case BaseColumnSet::DATA_TYPE_CHECK:
                echo $tableForm->row($model, $optionItems->column_name, $fieldOptions)->checkboxList($optionItems->subsetList);
                break;
            case BaseColumnSet::DATA_TYPE_DATE:
                echo $tableForm->row($model, $optionItems->column_name, $fieldOptions)->widget(FormattedDatePicker::className());
                break;
            case BaseColumnSet::DATA_TYPE_RADIO:
                echo $tableForm->row($model, $optionItems->column_name, $fieldOptions)->radioList($optionItems->subsetList);
                break;
            default:
                echo $tableForm->row($model, $optionItems->column_name, $fieldOptions)->textarea(['rows' => 5]); // todo とりあえずこうするが、入力文字数によって高さを調節するjsを導入
                break;
        }
    }
    //状態
    echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList(ClientMaster::getValidChkList());
    //運営元メモ
    echo $tableForm->row($model, 'admin_memo', $fieldOptions)->textarea();
    $tableForm->endTable();

    echo $this->render('/secure/common/_form-buttons.php', ['model' => $model]);
    TableForm::end(); ?>
</div>
