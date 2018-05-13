<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/05/25
 * Time: 10:13
 */
use yii\bootstrap\Html;
use yii\web\JsExpression;
use yii\widgets\Pjax;
use app\models\manage\Widget;
use app\models\manage\WidgetLayout;
use yii\widgets\ActiveForm;
use app\models\manage\ManageMenuMain;

/**
 * @var $this yii\web\View
 * @var $widget Widget
 * @var $arrayWidgets array
 */
$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

$css = <<<CSS
ul.sortable {
  background-color: #f5f5f5;
}
li.ui-sortable-handle {
  background-color: #ffffff;
}
.popover-content {
  max-width: 495px;
}
CSS;
$this->registerCss($css);

?>
<!--widget Sort-->
<h1 class="heading"><?= Html::icon('search') . Html::encode($this->title) ?></h1>
<div class="row">
    <div class="col-md-9">
        <p class="alert alert-warning">
            <?= Yii::t('app', '設定をクリックすると、ウィジェットの設定を編集できます。'); ?> <br>
            <b> <?= Yii::t('app', '各ウィジェットはドラッグ＆ドロップで並び替えられ、「配置を確定する」ボタンをクリックすることで配置の変更を確定します。') ?></b><br>
        </p>
        <?= Yii::$app->session->getFlash('operationComment') ?>
        <div id="showMessage"></div>
    </div>
</div>
<?php
ActiveForm::begin(['id' => 'form', 'action' => 'sort-update']);
?>
<p class="text-center">
    <?php
    echo Html::button(
        Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . Yii::t('app', 'PC版プレビュー'),
        [
            'class' => 'btn btn-warning mgt10 mgr20',
            'onclick' => new JsExpression('showPreview(false)'),
        ]
    );
    echo Html::button(
        Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . Yii::t('app', 'スマホ版プレビュー'),
        [
            'class' => 'btn btn-warning mgt10 mgr20',
            'onclick' => new JsExpression('showPreview(true)'),
        ]
    );
    echo Html::submitButton(
        '<i class="glyphicon glyphicon-ok"></i>' . Yii::t('app', '配置を確定する'),
        [
            'class' => 'btn btn-primary mgt10',
        ]
    );
    echo Html::hiddenInput('submitType', 'default', ['id' => 'submitType']);
    ?>
</p>

<div class="row">
    <div class="col-md-9 widgets-wrapper">
        <div class="row">
            <div class="widgetLayout widgetLayout1 col-md-12 search-inbox">
                <?= $this->render('_widget-layout', [
                    'arrayWidgets' => $arrayWidgets,
                    'widgetLayoutNo' => WidgetLayout::WIDGET_LAYOUT_NO_1,
                ]); ?>
            </div>
            <div class="widgetLayout widgetLayout2 col-md-8">
                <div class="row">
                    <div class="widgetLayout widgetLayout2-1 col-md-12 search-inbox">
                        <?= $this->render('_widget-layout', [
                            'arrayWidgets' => $arrayWidgets,
                            'widgetLayoutNo' => WidgetLayout::WIDGET_LAYOUT_NO_2,
                        ]); ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widgetLayout widgetLayout2-2 col-md-6 search-inbox">
                                <?= $this->render('_widget-layout', [
                                    'arrayWidgets' => $arrayWidgets,
                                    'widgetLayoutNo' => WidgetLayout::WIDGET_LAYOUT_NO_3,
                                ]); ?>
                            </div>
                            <div class="widgetLayout widgetLayout2-3 col-md-6 search-inbox">
                                <?= $this->render('_widget-layout', [
                                    'arrayWidgets' => $arrayWidgets,
                                    'widgetLayoutNo' => WidgetLayout::WIDGET_LAYOUT_NO_4,
                                ]); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="widgetLayout widgetLayout2-4 col-md-12 search-inbox">
                        <?= $this->render('_widget-layout', [
                            'arrayWidgets' => $arrayWidgets,
                            'widgetLayoutNo' => WidgetLayout::WIDGET_LAYOUT_NO_5,
                        ]); ?>
                    </div>
                </div>
            </div>
            <div class="widgetLayout widgetLayout3 col-md-4">
                <?= $this->render('_widget-layout', [
                    'arrayWidgets' => $arrayWidgets,
                    'widgetLayoutNo' => WidgetLayout::WIDGET_LAYOUT_NO_6,
                ]); ?>
            </div>
        </div>
    </div>
    <div class="col-md-3" id="fixedPoint">
        <div id="fixedBox" data-spy="affix" class="affix" style="width:20%;">
            <h3><?= Yii::t('app', '使用しないウィジェット') ?></h3>
            <p class="mgb20"><?= Yii::t('app', '使用しないウィジェットは下の欄に移動してください。') ?></p>
            <?= $this->render('_widget-layout', [
                'arrayWidgets' => $arrayWidgets,
                'widgetLayoutNo' => '',
            ]); ?>
        </div>
    </div>
    <!--
        end layout
    -->
</div>
<?php
ActiveForm::end();

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);
Pjax::end();

$newWindowSubmit =<<<JS
function showPreview(isMobile) {
  if (isMobile) {
    targetName = 'sp_preview';
    actionUrl = '/manage/secure/widget/preview?isMobile=1';
    window.open("about:blank",targetName,"width=600, height=950, scrollbars=yes");
  } else {
    targetName = 'pc_preview';
    actionUrl = '/manage/secure/widget/preview?isMobile=0';
    window.open("about:blank",targetName,"width=1000, height=950, scrollbars=yes");
  }
  
  var target = document.forms[0].target;
  var action = document.forms[0].action;
  document.forms[0].target = targetName;
  document.forms[0].action = actionUrl;
  document.forms[0].submit();
  document.forms[0].target = target;
  document.forms[0].action = action;
  document.getElementById("submitType").value = 'default';
}
JS;
$this->registerJs($newWindowSubmit, $this::POS_END);
