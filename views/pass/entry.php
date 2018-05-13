<?php

use yii\helpers\Html;
use proseeds\widgets\TableForm;

/* @var $this \yii\web\View */

$this->title                   = Yii::t('app', 'パスワード再設定');
$this->params['breadcrumbs'][] = ['label' => Html::encode('パスワード再設定')];
$this->params['bodyId']        = 'pass-entry';
?>
<div class="container subcontainer">
    <div class="row">
        <!-- Main Contents =============================================== -->
        <!-- Container =========================== -->

        <div class="col-sm-12">

            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= Yii::t('app', 'パスワード再設定') ?></h1>
                <div class="mod-subbox">
                    <p><?= Yii::t('app', 'パスワードを再設定します。新しいパスワードを入力してください。') ?></p>

                    <?php
                    $tableForm = TableForm::begin([
                        'action'       => 'entry-complete',
                        'options'      => ['enctype' => 'multipart/form-data', 'class' => 'mod-form1'],
                        'tableOptions' => ['class' => 'table mod-table1'],
                    ]);

                    $fieldOptions = [
                        'requiredTagOptions'  => ['class' => 'mod-label mod-label-required'],
                        'tagOptions'          => ['class' => 'mod-label mod-label-any'],
                        'template'            => "{th}\n{label}\n{/th}\n{td}\n{input}\n{hint}\n{error}\n{/td}",
                        'validateMarkOptions' => [],
                    ];

                    $tableForm->beginTable();

                    //パスワード
                    echo $tableForm->row($model, 'password', $fieldOptions)->textInput(['class' => 'input-txt-large']);
                    echo $tableForm->row($model, 'passwordRepeat', $fieldOptions)->textInput(['class' => 'input-txt-large']);

                    $tableForm->endTable();
                    ?>

                    <div class="btn-group">
                        <div class=" btn-group__center">
                            <?= Html::submitButton(Yii::t('app', 'パスワードの再設定'), ['class' => 'mod-btn2', 'name' => 'password-setting']) ?>
                        </div>
                    </div>

                    <?= Html::hiddenInput('key', $passwordReminder->collation_key) ?>

                    <?php TableForm::end(); ?><!--.mod-form1-loginform-->

                </div>
            </div>


        </div><!-- / .col-sm-12 -->

        <!-- / Main Contents =============================================== -->
    </div>
</div>