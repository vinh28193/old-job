<?php
use app\models\manage\JobColumnSet;
use app\models\manage\ClientColumnSet;
use app\models\manage\MainDisplay;

/* @var $this yii\web\View */
/* @var $dispTypeId integer */
/* @var $form yii\widgets\ActiveForm */
/* @var $bothListItems JobColumnSet[] */
/* @var $bothClientItems ClientColumnSet[] */
/* @var $mainDisplayModel MainDisplay */
$popoverCss = <<<CSS
.popover{
    max-width: 800px;
}
CSS;
$this->registerCss($popoverCss);

$collapseJS = <<<JS
     $(".panel-collapse").on("show.bs.collapse", function(){      
         var icon = $(this).parent('div').children('div.panel-heading').find('span');
         icon.removeClass('glyphicon glyphicon-menu-down');
         icon.addClass('glyphicon glyphicon-menu-up');
     });
     $(".panel-collapse").on("hide.bs.collapse", function(){
        var icon = $(this).parent('div').children('div.panel-heading').find('span');
        icon.removeClass('glyphicon glyphicon-menu-up');
        icon.addClass('glyphicon glyphicon-menu-down');
     });
JS;
$this->registerJs($collapseJS);
?>
<div class="panel panel-default">
    <div class="panel-heading" data-toggle="collapse" href="#mainDisp" style="cursor:pointer">
        <h4 class="panel-title">
            <?= Yii::t('app', '求人メイン項目') ?>
            <span class="glyphicon glyphicon-menu-down pull-right"></span>
        </h4>
    </div>
    <div id="mainDisp" class="panel-collapse collapse" style="height: auto;">
        <div class="panel-body">
            <div class="form-group">
                <div class="col-xs-12">
                    <?php
                    echo $this->render('_main-display', [
                        'dispTypeId' => $dispTypeId,
                        'mainDisplayModel' => $mainDisplayModel ?? [],
                        'form' => $form,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading" data-toggle="collapse" href="#listDisp" style="cursor:pointer">
        <h4 class="panel-title">
            <?= Yii::t('app', '求人リスト項目（募集要項）') ?>
            <span class="glyphicon glyphicon-menu-down pull-right"></span>
        </h4>

    </div>
    <div id="listDisp" class="panel-collapse collapse" style="height: auto;">
        <div class="panel-body">
            <div class="form-group">
                <div class="col-xs-12">
                    <?php echo $this->render('_list-display', [
                        'displayItems' => $bothListItems['listItems'] ?? [],
                        'notDisplayItems' => $bothListItems['notListItems'] ?? [],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading" data-toggle="collapse" href="#clientDisp" style="cursor:pointer">
        <h4 class="panel-title">
            <?= Yii::t('app', '求人リスト項目（企業情報）') ?>
            <span class="glyphicon glyphicon-menu-down pull-right"></span>
        </h4>
    </div>
    <div id="clientDisp" class="panel-collapse collapse" style="height: auto;">
        <div class="panel-body">
            <div class="form-group">
                <div class="col-xs-12">
                    <?php echo $this->render('_client-display', [
                        'displayItems' => $bothClientItems['clientItems'] ?? [],
                        'notDisplayItems' => $bothClientItems['notClientItems'] ?? [],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
