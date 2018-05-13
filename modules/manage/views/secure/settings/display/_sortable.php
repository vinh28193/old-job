<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/06/14
 * Time: 18:16
 */

use kartik\sortinput\SortableInput;
use yii\helpers\Html;

/** @var $items app\models\manage\JobColumnSet[] */
/** @var $name string */
/** @var $connected string */

$sortableItems = [];
foreach ($items as $item) {
    $listDispLabel = Html::encode($item->label);
    $content = <<<HTML
<div class="row text-center">
    <div class="col-md-12">
        {$listDispLabel}
    </div>
</div>
HTML;
    $sortableItems[$item->column_name] = [
        'content' => $content,
    ];
}

echo SortableInput::widget([
    'name' => $name,
    'items' => $sortableItems,
    'id' => $name,
    'sortableOptions' => [
        'itemOptions' => ['class' => 'ui-sortable-handle'],
        'class' => 'form-control',
        'connected' => $connected,
    ],
]);
