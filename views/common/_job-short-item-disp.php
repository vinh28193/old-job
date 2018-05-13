<?php

use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;

//応募先情報一覧のビュー
/* @var $model app\models\JobMasterDisp */
/* @var $dispTypeId int */
/* @var $headerMessage string */
$items = Yii::$app->functionItemSet->job->shortDisplayItems;
?>
<?php if(count($items) > 0): ?>
    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                <span class="pull-right fa fa-chevron-down mod-h2"></span>
                <h2 class="panel-title mod-h2"><?= $headerMessage ?></h2>
            </div>
            <div id="collapseOne" class="panel-collapse collapse">

                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => ArrayHelper::getColumn($items, 'formattedAttributeWithoutNewLine'),
                    'options' => ['class' => 'table mod-table1']
                ]); ?>

            </div>
        </div>
    </div>
<?php endif; ?>