<?php
use app\models\manage\JobColumnSet;
use proseeds\widgets\TableField;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $tableForm \proseeds\widgets\TableForm */
/* @var $model \app\models\manage\JobMaster */
/* @var $item \app\models\manage\JobColumnSet */

if ($item) {
    $rowOption = [
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
    ];
    /* @var $input TableField */
    $input = $tableForm->row($model, $item->column_name, $rowOption);
    switch ($item->data_type) {
        case JobColumnSet::DATA_TYPE_NUMBER:
        case JobColumnSet::DATA_TYPE_DATE:
        case JobColumnSet::DATA_TYPE_MAIL:
        case JobColumnSet::DATA_TYPE_URL:
            $input->textInput(['class' => 'form-control classic']);
            break;
        case JobColumnSet::DATA_TYPE_CHECK:
            $input->layout(function () use ($model, $tableForm, $item) {
                $items = ArrayHelper::map($item->subsetItems, 'subset_name', 'subset_name');
                $attribute = $item->column_name;
                echo Html::activeCheckboxList($model, $attribute, $items, [
                    'item' => function ($index, $label, $name, $checked, $value) use ($model, $attribute) {
                        $modelValue = explode(',', $model->$attribute);
                        return Html::label(
                            Html::checkbox($name, ArrayHelper::isIn($value, $modelValue), ['value' => $value]) . Html::encode($label),
                            null,
                            ['class' => 'checkbox-inline']
                        );
                    },
                    'class' => 'classic',
                ]);
            });
            break;
        case JobColumnSet::DATA_TYPE_RADIO:
        case JobColumnSet::DATA_TYPE_DROP_DOWN:
            $items = ArrayHelper::map($item->subsetItems, 'subset_name', 'subset_name');
            $input->dropDownList(['' => Yii::t('app', '--選択してください--')] + $items, ['class' => 'form-control classic']);
            break;
        case JobColumnSet::DATA_TYPE_TEXT:
            $input->textarea(['class' => 'form-control classic']);
            break;
        default:
            break;
    }

    // チェックボックスとドロップダウン（ラジオボタン）の時はヒントを表示しない
    if (!ArrayHelper::isIn($item->data_type, [
        JobColumnSet::DATA_TYPE_DROP_DOWN,
        JobColumnSet::DATA_TYPE_RADIO,
        JobColumnSet::DATA_TYPE_CHECK,
    ])){
        $input->hint($item->explain);
    }

    echo $input;
}
