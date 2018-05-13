<?php

use app\common\widget\FormattedDatePicker;
use app\models\manage\BaseColumnSet;
use app\models\manage\CorpColumnSet;
use yii\helpers\Url;
use proseeds\widgets\TableForm;
use proseeds\assets\BootBoxAsset;

BootBoxAsset::confirmBeforeSubmit($this, $model->isNewRecord ? Yii::t('app', '代理店情報を登録してもよろしいですか？') : Yii::t('app', '代理店情報を変更してもよろしいですか？'));

/* @var $this yii\web\View */
/* @var $model app\models\manage\CorpMaster */
/* @array $valid_chk app\models\SearchFunctionItemSet */
?>

<div class="corp-master-form">
    <?php

    $tableForm = TableForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'validationUrl' => Url::toRoute(['ajax-validation', 'id' => $model->id]),
//        'enableAjaxValidation' => true
    ]);
    $fieldOptions = [
        'tableHeaderOptions' => ['class' => 'm-column'],
    ];

    $tableForm->beginTable();

    /* @var $defaultItems CorpColumnSet[] */
    $defaultItems = Yii::$app->functionItemSet->corp->defaultItems;
    foreach ($defaultItems as $defaultItem) {
        if ($defaultItem->column_name == 'corp_no') {
            continue;
        } elseif ($defaultItem->column_name == 'corp_name') {
            echo $tableForm->row($model, $defaultItem->column_name, $fieldOptions + ['enableAjaxValidation' => true])->textInput();
        } else {
            echo $tableForm->row($model, $defaultItem->column_name, $fieldOptions)->textInput();
        }
    }


    foreach (Yii::$app->functionItemSet->corp->optionItems as $option) {
        switch ($option->data_type) {
            case BaseColumnSet::DATA_TYPE_NUMBER:
            case BaseColumnSet::DATA_TYPE_URL:
            case BaseColumnSet::DATA_TYPE_MAIL:
                echo $tableForm->row($model, $option->column_name, $fieldOptions)->textInput();
                break;
            case BaseColumnSet::DATA_TYPE_DROP_DOWN:
                echo $tableForm->row($model, $option->column_name, $fieldOptions)->dropDownList(['' => Yii::t('app', '(未選択)')] + $option->subsetList);
                break;
            case BaseColumnSet::DATA_TYPE_CHECK:
                echo $tableForm->row($model, $option->column_name, $fieldOptions)->checkboxList($option->subsetList);
                break;
            case BaseColumnSet::DATA_TYPE_DATE:
                echo $tableForm->row($model, $option->column_name, $fieldOptions)->widget(FormattedDatePicker::className());
                break;
            case BaseColumnSet::DATA_TYPE_RADIO:
                echo $tableForm->row($model, $option->column_name, $fieldOptions)->radioList($option->subsetList);
                break;
            default:
                echo $tableForm->row($model, $option->column_name, $fieldOptions)->textarea(['rows' => 5, 'options' => ['class' => 'form-control']]); // todo とりあえずこうするが、入力文字数によって高さを調節するjsを導入
                break;
        }
    }

    // 審査機能がONのとき代理店審査フラグの項目を表示
    if (Yii::$app->tenant->tenant->review_use) {
        echo $tableForm->row($model, 'corp_review_flg', $fieldOptions + ['enableAjaxValidation' => true])->radioList($model->formatTable['corp_review_flg']);
    }

    echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList($model->getValidChkList());

    $tableForm->endTable();
    ?>

    <?= $this->render('/secure/common/_form-buttons.php', ['model' => $model]); ?>

    <?php TableForm::end(); ?>
</div>
