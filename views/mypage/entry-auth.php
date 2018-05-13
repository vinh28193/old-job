<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use proseeds\widgets\TableForm;

/* @var \app\models\manage\ApplicationMaster $applicationModel */
/* @var app\models\JobMasterDisp $jobMaster */

$this->params['bodyId'] = 'entry-auth';

$validationJs = <<<JS
$("#form").on('afterValidateAttribute', function() {
  if ($("#nameData").find(".has-error").length) {
    $("#nameHeader > div").removeClass("has-success");
    $("#nameHeader > div").addClass("has-error");
  } else if ($("#nameData").find(".has-success").length == 2) {
    $("#nameHeader > div").removeClass("has-error");
    $("#nameHeader > div").addClass("has-success");
  }
});
JS;
$this->registerJs($validationJs);

//フォームのアイコン非表示
$this->registerCss('span.form-control-feedback{display: none;}');

$this->title = Yii::t('app', '応募確認')
?>
<div class="container subcontainer">
    <div class="row">
        <!--▼ここからコンテンツスタート▼-->
        <div class="col-sm-12">
            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= $this->title ?></h1>
                <div class="mod-subbox">

                    <p><?= Yii::t('app', '応募内容の確認には、下記の項目を入力してください。') ?></p>

                    <?php if (isset($errorMessage)): ?>
                        <div class="alert alert-warning" role="alert">
                            <?= $errorMessage ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    $tableForm    = TableForm::begin([
                        'id'           => 'form',
                        'options'      => [
                            'enctype' => 'multipart/form-data',
                            'class'   => 'mod-form1',
                        ],
                        'tableOptions' => ['class' => 'table mod-table1'],
                    ]);
                    $fieldOptions = [
                        'options'            => [],
                        'requiredTagOptions' => ['class' => 'mod-label mod-label-required'],
                        'tagOptions'         => ['class' => 'mod-label mod-label-any'],
                    ];
                    $tableForm->beginTable();
                    $tableForm->options += ['tableHeaderOptions' => [], 'tableDataOptions' => []];
                    echo $tableForm->row($model, 'applicationId', $fieldOptions)->textInput(['class' => 'form-control input-txt']);;
                    //氏名
                    echo $tableForm->field($model, 'fullName', array_merge($fieldOptions, [
                        'tableDataOptions'   => ['id' => 'nameData'],
                        'tableHeaderOptions' => ['id' => 'nameHeader'],
                    ]))->layout(function () use ($model, $tableForm, $fieldOptions) {
                        echo '<ul class="mod-form1 inline-text fullName">';
                        //姓
                        echo '<li class="field-nameSei">';
                        echo $tableForm->labelForm($model, 'nameSei', $fieldOptions)->textInput(['class' => 'form-control input-txt']);
                        echo '</li>';
                        //名
                        echo '<li class="field-nameMei">';
                        echo $tableForm->labelForm($model, 'nameMei', $fieldOptions)->textInput(['class' => 'form-control input-txt']);
                        echo '</li>';
                        echo '</ul>';
                    });
                    $tableForm->breakLine();
                    //メールアドレス
                    echo $tableForm->row($model, 'mailAddress',
                        $fieldOptions)->textInput(['class' => 'form-control input-txt input-txt-middle']);
                    ArrayHelper::remove($tableForm->options, 'tableDataOptions');
                    ArrayHelper::remove($tableForm->options, 'tableHeaderOptions');
                    ?>
                    <?php $tableForm->endTable() ?>
                    <div class="btn-group">
                        <div class=" btn-group__center">
                            <?= Html::submitButton(Yii::t('app', '応募内容を確認する'),
                                ['class' => 'mod-btn2', 'name' => 'act', 'value' => 'auth']) ?>
                        </div>
                    </div>
                    <?php TableForm::end(); ?>
                </div>
            </div>

            <!--▼ここでコンテンツエンド▼-->
        </div><!-- / .col-sm-12 -->
    </div>
</div>