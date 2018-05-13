<?php

use app\assets\CustomEditableAsset;
use app\assets\SlickSliderAsset;
use app\models\manage\JobMaster;
use app\models\manage\ListDisp;
use uran1980\yii\assets\TextareaAutosizeAsset;
use yii\helpers\Html;
use yii\web\View;
use app\assets\MainAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $mainDisps array */
/* @var $listDisps array */
/* @var $model JobMaster */

//申し込みプランIDが存在しないとき
if (is_null($listDisps)) {
    echo Html::tag('font', Yii::t('app', '※{planLabel}を選択してください。', ['planLabel' => $model->getAttributeLabel('client_charge_plan_id')]), ['size' => 5, 'color' => '#ff0000']);
    exit();
}

// 読み込み順の都合でAssetを先に読み込んでいます
CustomEditableAsset::register($this);
MainAsset::register($this);
TextareaAutosizeAsset::register($this);
SlickSliderAsset::register($this);

$hintCss = <<<CSS
.hint-block{
    color: #999;
    word-wrap: break-word;
    padding-top: 5px;
    padding-left: 1.3em;
    text-indent: -1em;
}
.hint-block span.glyphicon.glyphicon-info-sign{
    display: inline;
}
CSS;
$this->registerCss($hintCss);

// todo 他所をクリックしてもsubmit出来るように
$inputValueJs = <<<JS
    $(".editable").on("save", function(e, params){
        var inputId = this.id;
        if (inputId.indexOf("main-") == -1) {
            var parentId = inputId.replace("list-", "#jobmaster-");
            var brothorId = inputId.replace("list-", "#main-");
        } else {
            var parentId = inputId.replace("main-", "#jobmaster-");
            var brothorId = inputId.replace("main-", "#list-");
        }
        $(parentId, parent.document).val(params.newValue);
        $(brothorId).editable("setValue", params.newValue);
    });
    $('[data-toggle="tooltip"]').tooltip();
    //parent.$(parent.document).on('click', function(e) {
    //    $("form").editable('submit');
    //});
JS;
$this->registerJs($inputValueJs);

$picPjaxJs = <<<JS
$("img").on('click', function() {
    window.parent.renderPictureModalColtents(this.id);
});
JS;
$this->registerJs($picPjaxJs);
// todo jsエラーが出ないように
$tooltipJs = <<<JS
$(function () {
  $('[data-toggle="tooltip"]').tooltip({
    "triger" : "manual"
  });
  var shown = false;
  $('#showLabels', parent.document).on('click', function() {
  if (shown == false) {
      $('[data-toggle="tooltip"]').tooltip('show');
      shown = true;
    } else if(shown == true){
       $('[data-toggle="tooltip"]').tooltip('hide');
      shown = false;
    }
  });
});
JS;
$this->registerJs($tooltipJs);

$adjustPicJs = <<<JS
$(function () {
//setting slick.js
$(".slickNotSlide").slick({
    slidesToShow: 3,
    centerMode: true,
    responsive: [{
        breakpoint: 767,
        settings: {
            slidesToShow: 1,
            dots: true,
            centerMode: false
        }
    }]
});
});
JS;
$this->registerJs($adjustPicJs);

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class=''>
<?php $this->beginBody() ?>
<div class="container subcontainer flexcontainer">
    <div class="row">
        <div class="col-sm-12" style="margin-top: 40px;margin-right: 10px;margin-left: 10px;">
            <div class="mod-jobDetailBox clearfix">
                <div class="mod-excerptBox__header">
                    <?= $this->render('_preview-main-editable', ['model' => $model, 'mainDisps' => $mainDisps, 'mainDispName' => 'main']) ?>
                </div>
                <div class="mod-jobDetailBox__container">
                    <div class="mod-jobDetailBox__iconBox mod-iconBox">
                        <?php foreach (range(1, 6) as $i) {
                            $icons[] = Yii::t('app', '検索キーアイコン');
                        }
                        echo $this->render('@app/views/common/_searchkey-icons', ['searchKeyIconContents' => $icons]); ?>
                        <?= $this->render('_preview-main-editable', ['model' => $model, 'mainDisps' => $mainDisps, 'mainDispName' => 'title']) ?>
                        <?= $this->render('_preview-main-editable', ['model' => $model, 'mainDisps' => $mainDisps, 'mainDispName' => 'title_small']) ?>
                    </div>

                    <div class="mod-jobDetailBox__excerptBox mod-excerptBox excerptBox-primary">
                        <div class="mod-excerptBox__body clearfix">
                            <?= $this->render('_preview-pic', ['model' => $model, 'mainDisps' => $mainDisps, 'picId' => '1']); ?>
                            <div class="mod-excerptBox__excerpt">
                                <?= $this->render('_preview-main-editable', ['model' => $model, 'mainDisps' => $mainDisps, 'mainDispName' => 'comment']) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mod-jobDetailBox__excerptBox mod-excerptBox excerptBox-secondary">
                        <div class="mod-excerptBox__header">
                            <?= $this->render('_preview-main-editable', ['model' => $model, 'mainDisps' => $mainDisps, 'mainDispName' => 'main2']) ?>
                        </div>
                        <div class="mod-excerptBox__body clearfix">
                            <?= $this->render('_preview-pic', ['model' => $model, 'mainDisps' => $mainDisps, 'picId' => '2']); ?>
                            <div class="mod-excerptBox__excerpt">
                                <?= $this->render('_preview-main-editable', ['model' => $model, 'mainDisps' => $mainDisps, 'mainDispName' => 'comment2']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="mod-jobDetailBox__excerptBox mod-excerptBox excerptBox-pr">
                        <?= $this->render('_preview-main-editable', ['model' => $model, 'mainDisps' => $mainDisps, 'mainDispName' => 'pr']) ?>
                    </div>
                </div>


                <?php if (isset($mainDisps['pic3']) || isset($mainDisps['pic4']) || isset($mainDisps['pic5']) || isset($mainDisps['pic3_text']) || isset($mainDisps['pic4_text']) || isset($mainDisps['pic5_text'])): ?>
                    <div class="mod-jobDetailBox__flexcontainer">
                        <div class="mod-jobDetailBox__slider clearfix">
                            <ul class="mod-slider slickNotSlide">
                                <?= $this->render('_preview-pic-text', ['model' => $model, 'mainDisps' => $mainDisps, 'picId' => '3']) ?>
                                <?= $this->render('_preview-pic-text', ['model' => $model, 'mainDisps' => $mainDisps, 'picId' => '4']) ?>
                                <?= $this->render('_preview-pic-text', ['model' => $model, 'mainDisps' => $mainDisps, 'picId' => '5']) ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="mod-jobDetailBox__container">
                    <div class="mod-jobDetailBox__table">
                        <h2 id="jobinfo"><?= Yii::t('app', '募集要項') ?></h2>
                        <?php
                        echo DetailView::widget([
                            'model' => $model,
                            'options' => ['class' => 'table mod-table1'],
                            'attributes' => ListDisp::editableDetailAttributes($model, Yii::$app->request->queryParams['dispTypeId'])
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
<?php $this->endPage() ?>