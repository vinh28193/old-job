<?php
use kartik\sortinput\SortableInput;

/**
 * @var $arrayWidgets array
 * @var $widgetLayoutNo int|null
 */
$items = [];
foreach ($arrayWidgets[$widgetLayoutNo] ?? [] as $widget) {
    $content = $this->render('_widget-content', [
        'widget' => $widget,
    ]);
    $items[$widget->id] = ['content' => $content];
}

echo SortableInput::widget([
    'name' => 'widgetIds[' . $widgetLayoutNo . ']',
    'id' => "layout{$widgetLayoutNo}",
    'items' => $items,
    'sortableOptions' => [
        'itemOptions' => ['class' => 'ui-sortable-handle'],
        'connected' => true,
        'class' => 'form-control',
        'options' => ['style' => 'min-height:140px'],
    ],
]);
