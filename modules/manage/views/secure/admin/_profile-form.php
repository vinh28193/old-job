<?php

use app\models\manage\BaseColumnSet;
use yii\helpers\Url;
use proseeds\widgets\TableForm;
use proseeds\assets\BootBoxAsset;

/* @var $this yii\web\View */
/* @var $model app\models\manage\AdminMaster */
/* @var $form proseeds\widgets\TableForm */
/* @var $manageMenus */
/* @var $corpList array */
/* @var $clientList array */

BootBoxAsset::confirmBeforeSubmit($this, $model->isNewRecord ? Yii::t("app", "管理者情報を登録してもよろしいですか？") : Yii::t("app", "管理者情報を変更してもよろしいですか？"));

?>

<div class="corp-master-form">
    <?php
    $tableForm = TableForm::begin([
        'id' => 'form',
        'options' => ['enctype' => 'multipart/form-data'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'validationUrl' => Url::to(['ajax-validation', 'id' => $model->isNewRecord ? '0' : $model->id])
    ]);
    $tableForm->beginTable();
    //部署名・担当者名にて1セルに2つのinputを表示する。
    echo $tableForm->field($model, 'fullName')->layout(function () use ($model, $tableForm) {
        echo $tableForm->form($model, 'name_sei', ['inputOptions' => ['class' => 'form-control']])->textInput();
        echo '<br>';
        echo $tableForm->form($model, 'name_mei', ['inputOptions' => ['class' => 'form-control']])->textInput();
    });
    echo $tableForm->breakLine();
    echo $tableForm->row($model, 'login_id', ['enableAjaxValidation' => true])->textInput();
    echo $tableForm->row($model, 'password')->textInput();
    //電話番号
    echo $tableForm->row($model, 'tel_no')->textInput();
    //メールアドレス
    echo $tableForm->row($model, 'mail_address', ['enableAjaxValidation' => true])->textInput();
    //以下オプション
    foreach (Yii::$app->functionItemSet->admin->optionItems as $option) {
        switch ($option->data_type) {
            case BaseColumnSet::DATA_TYPE_NUMBER:
            case BaseColumnSet::DATA_TYPE_URL:
            case BaseColumnSet::DATA_TYPE_MAIL:
                echo $tableForm->row($model, $option->column_name)->textInput();
                break;
            case BaseColumnSet::DATA_TYPE_DROP_DOWN:
                echo $tableForm->row($model, $option->column_name)->dropDownList(["" => Yii::t("app", "(未選択)")] + $option->subsetList);
                break;
            case BaseColumnSet::DATA_TYPE_CHECK:
                echo $tableForm->row($model, $option->column_name)->checkboxList($option->subsetList);
                break;
            case BaseColumnSet::DATA_TYPE_DATE:
                echo $tableForm->row($model, $option->column_name)->widget(\yii\jui\DatePicker::className(), ['language' => 'ja', 'dateFormat' => 'yyyy-MM-dd']);
                break;
            case BaseColumnSet::DATA_TYPE_RADIO:
                echo $tableForm->row($model, $option->column_name)->radioList($option->subsetList);
                break;
            default:
                echo $tableForm->row($model, $option->column_name)->textarea(['rows' => 5]); // todo とりあえずこうするが、入力文字数によって高さを調節するjsを導入
                break;
        }
    }
    $tableForm->attributes = [];
    $tableForm->field($model, 'name_sei')->begin();
    $tableForm->field($model, 'name_mei')->begin();
    $tableForm->field($model, 'login_id', ['enableAjaxValidation' => true])->begin();
    $tableForm->field($model, 'password')->begin();
    $tableForm->field($model, 'tel_no')->begin();
    $tableForm->field($model, 'mail_address', ['enableAjaxValidation' => true])->begin();
    foreach (Yii::$app->functionItemSet->admin->optionItems as $option) {
        $tableForm->field($model, $option->column_name)->begin();
    }
    $tableForm->endTable();
    ?>

    <?= $this->render('/secure/common/_form-buttons.php', [
        'model' => $model
    ]); ?>

    <?php TableForm::end(); ?>
</div>