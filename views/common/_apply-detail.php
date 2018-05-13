<?php

use yii\widgets\DetailView;

//応募情報一覧のビュー
/* @var $this \yii\web\View */
/* @var $model app\models\Apply */
/* @var $dispTypeId int */
/* @var $headerMessage string */

$attributes = Yii::$app->functionItemSet->application->applyItemColumns;
?>
<?php if(count($attributes) > 0): ?>
    <h2 class="mod-h4"><?= $headerMessage ?></h2>
    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
        'options' => [
            'class' => 'table mod-table1',
        ]
    ]);
    ?>
<?php endif; ?>