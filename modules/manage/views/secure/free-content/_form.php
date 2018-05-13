<?php

use app\common\widget\DynamicFormWidget;
use app\modules\manage\models\forms\FreeContentElementForm;
use app\modules\manage\models\forms\FreeContentForm;
use kartik\widgets\FileInput;
use proseeds\assets\BootBoxAsset;
use proseeds\widgets\TableForm;
use uran1980\yii\assets\TextareaAutosizeAsset;
use yii\helpers\Html;
use yii\helpers\Url;

BootBoxAsset::confirmBeforeSubmit($this, $model->isNewRecord ? Yii::t('app', 'フリーコンテンツを登録してもよろしいですか？') : Yii::t('app', 'フリーコンテンツを変更してもよろしいですか？'));
TextareaAutosizeAsset::register($this);

/* @var $this yii\web\View */
/* @var $model FreeContentForm */

$widgetContainer = 'dynamicform_wrapper';
$widgetBody = 'container-elements';
$widgetItem = 'element';
$insertButton = 'add-element';
$deleteButton = 'remove-element';
$formId = 'form';
$formName = 'input_form'

?>

    <div class="free-content-form">
        <?php $form = TableForm::begin([
            'id' => $formId,
            'options' => [
                'enctype' => 'multipart/form-data',
                'name' => $formName,
            ],
            'tableOptions' => ['class' => 'table table-bordered'],
            'validationUrl' => Url::to(['ajax-validation', 'id' => $model->id]),
        ]); ?>

        <?php
        $form->beginTable();
        echo $form->row($model, 'title')->textInput();
        echo $form->row($model, 'keyword')->textInput();
        echo $form->row($model, 'description')->textInput();
        echo $form->row($model, 'url_directory', ['enableAjaxValidation' => true])->textInput();
        echo $form->row($model, 'valid_chk')->radioList(FreeContentForm::validArray());
        $form->endTable();
        ?>


        <?php DynamicFormWidget::begin([
            'widgetContainer' => $widgetContainer,
            'widgetBody' => '.' . $widgetBody,
            'widgetItem' => '.' . $widgetItem,
            'limit' => 20,
            'min' => 1,
            'insertButton' => '.' . $insertButton,
            'deleteButton' => '.' . $deleteButton,
            'model' => $model->elementModels[0],
            'formId' => $formId,
            'formFields' => [
                'displayItem',
                'layout',
                'text',
                'imgFile',
                'image_file_name',
                'base64Img',
            ],
        ]); ?>
        <div class="panel panel-default">
            <div class="panel-body <?= $widgetBody ?>"><!-- widgetContainer -->
                <?php foreach ($model->elementModels as $index => $elementModel) : ?>
                    <div class="<?= $widgetItem ?> panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <?php
                            $elementIds[] = $elementModel->id;
                            echo $form->parentField($elementModel, "[{$index}]id", ['template' => '{input}'])->hiddenInput()
                            ?>
                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->parentField($elementModel, "[{$index}]displayItem", [
                                        'template' => "{label}<div class='col-sm-8'>{input}</div>{error}",
                                        'options' => ['class' => 'row'],
                                        'labelOptions' => ['class' => 'col-sm-4 control-label'],
                                        'drawRequireLabel' => false,
                                    ])->dropDownList(FreeContentElementForm::displayItemLabels(), ['class' => 'displayItem form-control']) ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->parentField($elementModel, "[{$index}]layout", [
                                        'template' => "{label}<div class='col-sm-8'>{input}</div>{error}",
                                        'options' => ['class' => 'row', 'style' => 'display: none'],
                                        'labelOptions' => ['class' => 'col-sm-4 control-label'],
                                        'drawRequireLabel' => false,
                                    ])->dropDownList(FreeContentElementForm::layoutLabels(), ['class' => 'layout form-control']) ?>
                                </div>
                                <div class="col-sm-3"></div>
                                <div class="col-sm-1">
                                    <button type="button" class="pull-right <?= $deleteButton ?> btn btn-default btn-xs"><?= Yii::t('app', '削除する')?></button>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div style="display: none">
                                        <?= $form->parentField($elementModel, "[{$index}]imgFile", [
                                            'drawRequireLabel' => false,
                                            'template' => "{label}\n{input}\n{hint}\n{error}",
                                            'labelOptions' => [
                                                'label' => "{$elementModel->getAttributeLabel('imgFile')}（幅<span class='recommendedWidth'>{$elementModel->recommendedWidth()}</span>px×高さ（任意））",
                                            ],
                                        ])->widget(FileInput::className(), [
                                            'model' => $elementModel,
                                            'attribute' => "[{$index}]imgFile",
                                            'pluginOptions' => array_merge([
                                                'showCaption' => false,
                                                'showUpload' => false,
                                                'showRemove' => false,
                                                'showClose' => false,
                                                'layoutTemplates' => ['footer' => '', 'actions' => ''],
                                            ], $elementModel->pluginOptionsForInit()),
                                        ]); ?>

                                        <?= Html::activeHiddenInput($elementModel, "[{$index}]base64Img") ?>
                                        <?= Html::activeHiddenInput($elementModel, "[{$index}]image_file_name") ?>
                                        <?php
                                        // todo TableFormのclientValidationを複数回するバグが消え次第削除
                                        /** @noinspection PhpInternalEntityUsedInspection */
                                        array_pop($form->attributes);
                                        /** @noinspection PhpInternalEntityUsedInspection */
                                        array_pop($form->attributes);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <?= $form->parentField($elementModel, "[{$index}]text", [
                                        'template' => '{label}{input}{error}',
                                        'labelOptions' => ['class' => 'col-sm-4 control-label'],
                                        'drawRequireLabel' => false,
                                        'successMarkClass' => null,
                                        'failMarkClass' => null,
                                    ])->textarea(['rows' => 1]); ?>
                                    <?php
                                    // todo TableFormのclientValidationを複数回するバグが消え次第削除
                                    /** @noinspection PhpInternalEntityUsedInspection */
                                    array_pop($form->attributes);
                                    /** @noinspection PhpInternalEntityUsedInspection */
                                    array_pop($form->attributes);
                                    ?>
                                </div>

                            </div><!-- end:row -->
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <button type="button" class="center-block <?= $insertButton ?> btn btn-danger btn-sm"><?= Yii::t('app', '要素追加')?></button>
                </div>
            </div>
        </div>
        <?php DynamicFormWidget::end(); ?>

        <?= Html::hiddenInput('elementIds', implode(',', $elementIds ?? [])) ?>

        <p class="text-right">
            <?php
            // todo 求人原稿編集画面からコピペしてきたが、js共々asset化してwidget化する必要がある
            echo Html::submitButton(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . Yii::t('app', 'PC版プレビュー'),
                [
                    'class' => 'btn btn-warning mgt10 mgr20',
                    'onclick' => 'document.getElementById("submitType").value = "pcSubmit";',
                ]
            );
            echo Html::submitButton(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . Yii::t('app', 'スマホ版プレビュー'),
                [
                    'class' => 'btn btn-warning mgt10',
                    'onclick' => 'document.getElementById("submitType").value = "spSubmit";',
                ]
            );
            echo Html::hiddenInput('submitType', 'default', ['id' => 'submitType']);
            ?>
        </p>

        <?php
        echo $this->render('/secure/common/_form-buttons.php', ['model' => $model]);
        TableForm::end();
        ?>
    </div>

<?php
$hasImg = FreeContentElementForm::HAS_IMG;
$displayText = FreeContentElementForm::DISPLAY_TEXT;
$displayImg = FreeContentElementForm::DISPLAY_IMG;
$displayBoth = FreeContentElementForm::DISPLAY_BOTH;
$withOnlyImg = FreeContentElementForm::WIDTH_ONLY_IMG;
$withBoth = FreeContentElementForm::WIDTH_BOTH;

$js = <<<JS
(function ($) {
  /**
   * inputの表示を切り変える
   * @param displayItem {object}
   */
  function changeElementDisplay(displayItem) {
    var thisId = displayItem.id; 
    var layout = $(".field-" + thisId.replace(/displayitem/g, "layout"));
    var text = $(".field-" + thisId.replace(/displayitem/g, "text"));
    var img = text.parent().prev().children();

    if (displayItem.value == {$displayText}) {
      layout.hide();
      text.show();
      img.hide();
      text.parent().removeClass().addClass("col-sm-12");
    } else if(displayItem.value == {$displayImg}) {
      layout.hide();
      text.hide();
      img.show();
      img.find(".recommendedWidth").text({$withOnlyImg});
      img.parent().removeClass().addClass("col-sm-12");
    } else if (displayItem.value == {$displayBoth}) {
      layout.show();
      text.show();
      img.show();
      img.find(".recommendedWidth").text({$withBoth});
      text.parent().removeClass().addClass("col-sm-6");
      img.parent().removeClass().addClass("col-sm-6");
    }
  }

  /** 表示の切り替えをドロップダウンのchangeイベントにバインドし直す */
  function displayItemEvent() {
    $(".displayItem").off("change");
    $(".displayItem").on("change", function() {
      changeElementDisplay(this);
    });
  }
  
  /** hasImgとbase64Imgの更新をFileInputのfileselectイベントにバインドし直す */
  function updateImgEvent() {
    $("input[type='file']").off("fileselect");
    $("input[type='file']").on("fileselect fileselectnone", function(e) {
      // image_file_nameのクリア
      var fileInputId = this.id.replace(/imgfile/g, "image_file_name");
      $("#" + fileInputId).val('');
      // base64の更新
      var base64ImgId = this.id.replace(/imgfile/g, "base64img");
      if(e.type == "fileselect") {
        updateBase64Img(e, base64ImgId);
      } else {
        $("#" + fileInputId).val("");
      }
      // validationする
      $("#{$formId}").yiiActiveForm("validateAttribute", fileInputId);
    });
  }
  
  function updateBase64Img(e, base64ImgId) {
    var file = e.target.files[0];
    var fr = new FileReader();
    fr.readAsDataURL(file);
    fr.onload = function(e) {
      $("#" + base64ImgId).val(e.target.result);
    }
  }

  // 表示を初期化
  $(".displayItem").each(function(index, element) {
    changeElementDisplay(element);
  });
  
  // 表示切替とhasImgの更新のバインドを初期化
  displayItemEvent();
  updateImgEvent();
  
  // 表示切替とhasImgの更新のバインド処理を要素追加イベントにバインド
  $(".{$widgetContainer}").on("afterInsert", function(e, item) {
    displayItemEvent();
    updateImgEvent();
    $(item).find("input[type='file']").fileinput('clear');
  });

  // 並び替え
  $(".{$widgetBody}").sortable({
    items: ".element",
    cursor: "move",
    opacity: 0.6,
    axis: "y",
    handle: ".panel-heading",
    update: function(ev){
      $(".{$widgetContainer}").yiiDynamicForm("updateContainer");
    }
  }).disableSelection();

})(window.jQuery);
JS;
$this->registerJs($js);

// todo ヘッダーフッターからコピペしてきたが、php共々asset化してwidget化する必要がある
// todo validation失敗時にsubmitTypeが変になる件が暫定処置なので、共通化のタイミングで全体的に見直す必要がある
// @see @app/modules/manage/views/secure/settings/header-footer-html/update.php
// @see @app/modules/manage/views/secure/job/form/_form.php
$newWindowSubmit = <<<JS
(function($) {
  $("button[name='complete']").on('click', function() {
    $('#submitType').val('default');
  })
  $('#form').on('beforeSubmit', function(e){
    //どのsubmitボタンをクリックしたのか
    var submitType = $('#submitType').val();
    if (submitType == 'pcSubmit' || submitType == 'spSubmit') {
      var targetName = '';
      var actionUrl = '';
      if(submitType == 'spSubmit'){
        targetName = 'sp_preview';
        actionUrl = "/manage/secure/free-content/form-preview?mode=Mobile";
        window.open("about:blank",targetName,"width=600, height=950, scrollbars=yes");
      } else if (submitType == 'pcSubmit') {
        targetName = 'pc_preview';
        actionUrl = "/manage/secure/free-content/form-preview?mode=PC";
        window.open("about:blank",targetName,"width=1000, height=950, scrollbars=yes");
      } else {
        return false;
      }
      var target = document.{$formName}.target;
      var action = document.{$formName}.action;
    
      document.{$formName}.target = targetName;
      document.{$formName}.action = actionUrl;
      document.{$formName}.submit();

      document.{$formName}.target = target;
      document.{$formName}.action = action;
      document.getElementById("submitType").value = 'default';
            
      //以降の処理を中断
      e.stopImmediatePropagation();
      } else {
        // ポスト前にbase64を消去
        $('#base64Url').val('');
      }
    return false;
  });

})(jQuery);
JS;
$this->registerJs($newWindowSubmit, $this::POS_END);
?>