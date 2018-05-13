<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/06/14
 * Time: 18:16
 */

use app\models\manage\MainDisplay;
use kartik\sortinput\SortableInput;

/** @var $model MainDisplay */
/** @var $attribute string */
/** @var $form yii\widgets\ActiveForm */

if (isset($model->mainItems[$attribute])) {
    $content = <<<HTML
<div class="row text-center">
    <div class="col-md-12">
        {$model->mainItems[$attribute]->label}
    </div>
</div>
HTML;

    $items = [$model->$attribute => ['content' => $content]];
} else {
    $items = [];
}

echo $form->field($model, $attribute, ['template' => '{input}{error}'])->widget(SortableInput::className(), [
    'name' => "MainDisplaySetting[\"{$attribute}\"]",
    'items' => $items,
    'id' => 'test',
    'sortableOptions' => [
        'itemOptions' => ['class' => 'ui-sortable-handle'],
        'class' => 'form-control',
        'connected' => 'main-item',
    ],
    'options' => ['class' => 'form-control main-display'],
]);
