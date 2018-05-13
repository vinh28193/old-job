<?php

use app\models\manage\MainVisual;
use app\modules\manage\models\requests\MainVisualForm;
use app\modules\manage\models\requests\MainVisualImageForm;
use proseeds\widgets\TableForm;
use yii\bootstrap\Html;
use kartik\widgets\FileInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model MainVisualForm */
/* @var $image MainVisualImageForm */
?>
<div id="tab-form-<?= $model->area->id ?? 0; ?>" class="corp-master-form">
    <?php $tableForm = TableForm::begin([
        'action' => Url::to([
            'secure/main-visual/form',
            'areaId' => $model->area->id ?? 0,
            'id' => $model->mainVisual->id,
        ]),
        'options' => ['enctype' => 'multipart/form-data'],
        'tableOptions' => ['class' => 'table table-bordered'],
    ]) ?>

    <br>

    <?= $tableForm->form($model, 'area_id')->hiddenInput(['value' => $model->area->id ?? '']); ?>

    <?php $tableForm->beginTable(); ?>

    <?= $tableForm->row($model, 'type')->dropDownList(MainVisual::types(), [
        'prompt' => Yii::t('app', '選択'),
        'class' => 'form-control type-choice',
    ]); ?>

    <?= $tableForm->row($model, 'valid_chk')->radioList(MainVisualForm::validFlags(), [
        'value' => $model->valid_chk ?? 0,
    ]); ?>

    <?= $tableForm->row($model, 'memo')->textarea(); ?>

    <?php $tableForm->endTable(); ?>

    <?php foreach ($model->images as $i => $image): ?>

        <div id="images-<?= $model->area->id ?? 0 ?>_<?= $i; ?>" class="image-form">
            <h3 class="pd5"><?= Yii::t('app', '画像') ?><?= Html::encode($i + 1) ?></h3>

            <?= $tableForm->form($image, 'id')->hiddenInput(); ?>

            <?php $tableForm->beginTable(); ?>

            <?= $tableForm->row($image, 'file', [
                'hint' => Yii::t('app', 'PC版推奨サイズ（1140px × 350px）'),
            ])->widget(
                FileInput::className(),
                [
                    'options' => [
                        'accept' => 'image/*',
                    ],
                    'pluginOptions' => [
                        'showCaption' => false,
                        'showUpload' => false,
                        'showRemove' => false,
                        'showClose' => false,
                        'layoutTemplates' => ['footer' => '', 'actions' => ''],
                        'initialPreview' => [
                            $image->mainVisualImage->file_name ? $image->srcUrl() : null
                        ],
                        'initialPreviewAsData' => !$image->mainVisualImage->isNewRecord,
                    ],
                ]
            )->isRequired(empty($i)); ?>

            <?= $tableForm->row($image, 'url')->input('text', [
                'class' => 'form-control url_' . $i,
            ]); ?>

            <?= $tableForm->row($image, 'file_sp', [
                'hint' => Yii::t('app', 'SP版推奨サイズ（640px × 200px）'),
            ])->widget(
                FileInput::className(),
                [
                    'options' => [
                        'accept' => 'image/*',
                    ],
                    'pluginOptions' => [
                        'showCaption' => false,
                        'showUpload' => false,
                        'showRemove' => false,
                        'showClose' => false,
                        'layoutTemplates' => ['footer' => '', 'actions' => ''],
                        'initialPreview' => [
                            $image->mainVisualImage->file_name_sp ? $image->srcSpUrl() : null
                        ],
                        'initialPreviewAsData' => !$image->mainVisualImage->isNewRecord,
                    ],
                ]
            )->isRequired(empty($i)); ?>

            <?= $tableForm->row($image, 'url_sp')->input('text', [
                'class' => 'form-control url_sp_' . $i,
            ]); ?>

            <?= $tableForm->row($image, 'content')->input('text', [
                'class' => 'form-control content_' . $i,
            ]); ?>

            <?= $tableForm->row($image, 'sort')->dropDownList(MainVisualForm::orders()); ?>

            <?= $tableForm->row($image, 'valid_chk')->radioList(MainVisualForm::validFlags(), [
                'value' => $image->valid_chk ?? 0,
            ]); ?>

            <?php $tableForm->endTable(); ?>

        </div>

    <?php endforeach; ?>

    <?php $tableForm->endTable(); ?>

    <div class="form-group" style="text-align: center">
        <?= Html::submitButton(
            Html::icon('pencil') . Yii::t('app', '保存'),
            [
                'class' => 'btn btn-primary btn-lg w100s w50m w50l mgt10 mgl20',
                'name' => 'complete',
            ]
        ); ?>
        <br>
        <br>
        <?= Html::tag(
            'span',
            Yii::t('app', '※「保存する」ボタンをクリックすると、現在表示中のエリアの画像のみ登録・更新されます。')
        ) ?>
    </div>


    <?php TableForm::end(); ?>
</div>

