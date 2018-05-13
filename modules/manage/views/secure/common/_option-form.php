<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/30
 * Time: 15:58
 */

use app\models\manage\ApplicationColumnSet;
use app\models\manage\BaseColumnSet;
use app\models\manage\ClientColumnSet;
use app\models\manage\InquiryColumnSet;
use app\models\manage\JobColumnSet;
use app\modules\manage\controllers\secure\OptionBaseController;
use proseeds\assets\PjaxModalAsset;
use proseeds\widgets\TableForm;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use uran1980\yii\assets\TextareaAutosizeAsset;

/* @var $this yii\web\View */
/** @var \app\models\manage\BaseColumnSet $model */

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.showModal',
]);

PjaxModalAsset::register($this);
$this->registerJs('$("#modal").modal("show");');

/** @var TableForm $tableForm */
$tableForm = TableForm::begin([
    'id' => 'updateForm',
    'action' => Url::to(['/' . str_replace('pjax-modal', 'update', Yii::$app->requestedRoute), 'id' => $model->id]),
    'options' => ['enctype' => 'multipart/form-data'],
    'tableOptions' => ['class' => 'table table-bordered'],
]);
Modal::begin([
    'id' => 'modal',
    'header' => Yii::t('app', '項目変更'),
    'footer' => Html::button(Yii::t('app', '閉じる'),
            ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal']
        ) . ' ' . Html::submitButton(Yii::t('app', '変更'), ['class' => 'btn btn-sm btn-primary submitUpdate']),
]);

$tableForm->beginTable();
// ラベル
if (ArrayHelper::isIn($model->column_name, $model::STATIC_LABEL)) {
    echo $tableForm->row($model, 'label')->text();
} else {
    echo $tableForm->row($model, 'label')->textInput();
}

TextareaAutosizeAsset::register($this);
//項目説明文
if ($model->hasAttribute('column_explain') && !ArrayHelper::isIn($model->column_name, $model::STATIC_COLUMN_EXPLAIN)) {
    if (ArrayHelper::isIn($model->column_name, $model::FULL_NAME)) {
        echo $tableForm->row($model, 'columnExplainSei')->textInput();
        echo $tableForm->row($model, 'columnExplainMei')->textInput();
    } else {
        if ($model::className() == ApplicationColumnSet::className() && ArrayHelper::isIn($model->column_name, ApplicationColumnSet::STRING)) {
            echo $tableForm->row($model, 'column_explain')->textInput();
        } else {
            echo $tableForm->row($model, 'column_explain')->textarea(['rows' => 2]);
        }
    }
}

// 入力タイプ
if (ArrayHelper::isIn($model->column_name, $model::STATIC_DATA_TYPE)) {
    echo $tableForm->row($model, 'data_type')->text();
} else {
    echo $tableForm->row($model, 'data_type')->radioList($model->typeArray);
}
// 選択項目
if ($subsetModel = $model->getSubset()) {
    $count = count((array)$model->subsetItems);
    $ulId = OptionBaseController::SUBSET_UL_ID;
    $initSubset = <<<JS
$("#{$ulId}").find(":last").after('<input type="button" class="btn btn-sm btn-default add" value="追加">');
if ({$count} === 1) {
  $(".remove").remove();
}
subsetindex = {$count};
if ({$count} == 0) {
  $("#updateForm-subset_name-tr").hide();

} else {
  $("#updateForm-max_length-tr").hide();
  $("#updateForm-column_explain-tr").hide();
}

$("#updateForm").on("afterValidateAttribute.yiiActiveForm", function(event, attribute, messages){
    setTimeout(function(){
        var inputCount = $(".multi-data-column-subset_name").find("input[type='text']").length;
        var errorCount = $(".multi-data-column-subset_name").find(".has-error").length;
        var successCount = $(".multi-data-column-subset_name").find(".has-success").length;

        var labelWrap = $(".add-input-wrap:first");
        $(labelWrap).removeClass("has-error");
        $(labelWrap).removeClass("has-success");
        if (errorCount > 0) {
            $(labelWrap).addClass("has-error");
        } else if (inputCount == successCount) {
            $(labelWrap).addClass("has-success");
        }
    }, 50);
});

JS;
    $this->registerJs($initSubset);
    echo $tableForm->row(new $subsetModel, 'subset_name', ['options' => ['class' => 'add-input-wrap']])->layout(function () use ($tableForm, $model, $ulId) {
        echo Html::beginTag('ul', ['id' => $ulId, 'class' => 'multi-data-column-subset_name overlap-subset_name']);
        foreach ($model->subsetItems as $i => $subset) {
            echo '<li>';
            echo $tableForm->form($subset, "[$i]subset_name", ['options' => ['class' => 'add-input-form']])->textInput();
            echo '<div class="add-input-btn-wrap"><input type="button" class="btn btn-sm btn-default remove" value="削除"></div>';
            echo '</li>';
        }
        echo Html::endTag('ul');
    });
}
// 入力必須か否か
if ($model->column_name == 'exceptions') {
    echo $tableForm->row($model, 'is_must')->isRequired(true)->layout(function () {
        echo ArrayHelper::getValue(BaseColumnSet::getIsMustArray(), 0);
    });
} elseif (ArrayHelper::isIn($model->column_name, $model::STATIC_IS_MUST)) {
    echo $tableForm->row($model, 'is_must')->isRequired(true)->text();
} else {
    echo $tableForm->row($model, 'is_must')->isRequired(true)->radioList($model::getIsMustArray());
}

// 最大入力制限
if (ArrayHelper::isIn($model->column_name, $model::STATIC_MAX_LENGTH)) {
    echo $tableForm->row($model, 'max_length')->isRequired(true)->text();
} else {
    echo $tableForm->row($model, 'max_length')->isRequired(true)->textInput();
}
// 管理画面一覧表示するか否か
if (ArrayHelper::isIn($model->column_name, $model::STATIC_IS_IN_LIST)) {
    echo $tableForm->row($model, 'is_in_list')->isRequired(true)->text();
} elseif (!$model instanceof InquiryColumnSet) {
    echo $tableForm->row($model, 'is_in_list')->isRequired(true)->radioList($model::getIsInListArray());
}
// 管理画面でキーワード検索できるか否か
if (ArrayHelper::isIn($model->column_name, $model::STATIC_IS_IN_SEARCH)) {
    echo $tableForm->row($model, 'is_in_search')->isRequired(true)->text();
} elseif (!$model instanceof InquiryColumnSet) {
    echo $tableForm->row($model, 'is_in_search')->isRequired(true)->radioList($model::getIsInSearchArray());
}
// 有効or無効
if (ArrayHelper::isIn($model->column_name, $model::STATIC_VALID_CHK)) {
    echo $tableForm->row($model, 'valid_chk')->text();
} else {
    echo $tableForm->row($model, 'valid_chk')->radioList($model::getValidArray());
}

// 対象or対象外
if ($model->hasAttribute('freeword_search_flg')) {
    /** @var JobColumnSet|ClientColumnSet $model */
    if (ArrayHelper::isIn($model->column_name, $model::STATIC_FREEWORD_SEARCH_FLG)) {
        echo $tableForm->row($model, 'freeword_search_flg')->isRequired(true)->text();
    } else {
        echo $tableForm->row($model, 'freeword_search_flg')->isRequired(true)->radioList($model::getFreewordSearchFlgArray());
    }
}

$tableForm->endTable();

Modal::end();
TableForm::end();
Pjax::end();
