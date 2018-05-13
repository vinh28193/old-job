<?php

use app\common\PostablePjax;
use app\common\widget\FormattedDatePicker;
use app\models\manage\ManageMenuMain;
use app\models\manage\Widget;
use app\modules\manage\controllers\secure\WidgetDataController;
use kartik\widgets\FileInput;
use proseeds\widgets\TableForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\manage\WidgetData */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ウィジェットデータ一覧・編集'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>
<h1 class="heading"><?= Html::icon($menu->icon_key) . Html::encode($this->title) ?></h1>
<div class="container">
    <div class="row">
        <div class="col-md-10" role="complementary">
            <?php
            echo Html::dropDownList(
                'widgetId',
                null,
                Widget::getDropDownArray(),
                ['id' => 'widgetId', 'class' => 'form-control jq-placeholder', 'prompt' => Yii::t('app', '--選択してください--')]
            );
            PostablePjax::begin([
                'id' => WidgetDataController::PJAX_ID,
                'enablePushState' => false,
                'url' => Url::toRoute('input-fields'),
                'trigger' => ['selector' => '"#widgetId"', 'event' => 'change'],
                'postAttribute' => 'value',
                'postAttributeName' => 'widgetId',
            ]);
            $tableForm = TableForm::begin([
                'id' => 'form',
                'options' => ['enctype' => 'multipart/form-data'],
                'tableOptions' => ['class' => 'table table-bordered'],
            ]);
            // pjax遷移先で使うjsのcss生成のために仮置き
            $tableForm->form($model, 'pict')->widget(FileInput::className(), [
                'pluginOptions' => [
                    'showCaption' => false,
                    'showPreview' => false,
                    'showRemove' => false,
                    'showUpload' => false,
                    'showCancel' => false,
                    'showClose' => false,
                    'showUploadedThumbs' => false,
                    'browseClass' => 'hidden',
                    'layoutTemplates' => ['actions' => '',],
                ]
            ]);
            $tableForm->row($model, 'disp_start_date', [
                'enableAjaxValidation' => true,
                'inputOptions' => ['class' => 'form-control limit_num']
            ])->widget(FormattedDatePicker::className(), [
                'options' => ['class' => 'form-control disp_start_date',]
            ]);
            TableForm::end();
            PostablePjax::end(); ?>
        </div>
    </div>
</div>
