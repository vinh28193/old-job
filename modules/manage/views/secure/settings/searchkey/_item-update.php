<?php
use app\models\manage\SearchkeyMaster;
use proseeds\assets\PjaxModalAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use proseeds\widgets\TableForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $model app\models\manage\SearchkeyMaster */
/* @var $this \yii\web\View */

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);
$this->registerCss('.popover{
    max-width: 100%; /* Max Width of the popover (depending on the container!) */
}');
PjaxModalAsset::register($this);
$this->registerJs('$("#modal").modal("show");');
$this->registerCss('img {max-width : 100%;}');


$tableForm = TableForm::begin([
    'id' => 'form',
    'action' => Url::to(['update', 'id' => $model->id]),
    'options' => ['enctype' => 'multipart/form-data'],
    'tableOptions' => ['class' => 'table table-bordered'],
]);
Modal::begin([
    'id' => 'modal',
    'header' => Yii::t('app', '検索キー設定変更'),
    'size' => Modal::SIZE_LARGE,
    'footer' => Html::button(Yii::t('app', '閉じる'), ['class' => 'btn btn-sm btn-default', 'data-dismiss' => 'modal']) . ' ' . Html::submitButton(Yii::t('app', '変更'), ['class' => 'btn btn-sm btn-primary submitUpdate']),
]);
$tableForm->beginTable();

echo $tableForm->row($model, 'searchkey_name')->textInput();

echo $tableForm->row($model, 'sort')->textInput();

echo $tableForm->row($model, 'is_on_top')->isRequired(true)->radioList($model->getIsOnTop());

if (in_array($model->table_name, SearchkeyMaster::STATIC_KEYS)) {
    echo $tableForm->row($model, 'is_and_search')->isRequired(true)->text();
} else {
    echo $tableForm->row($model, 'is_and_search')->isRequired(true)->radioList($model->getIsAndSearch());
}

if (in_array($model->table_name, SearchkeyMaster::STATIC_KEYS) || $model->principal_flg == 1) {
    echo $tableForm->row($model, 'search_input_tool', [
        'popoverHintOptions' => ['dataContainer' => '.modal-content'],
    ])->isRequired(true)->text();
} else {
    echo $tableForm->row($model, 'search_input_tool', [
        'popoverHintOptions' => ['dataContainer' => '.modal-content'],
    ])->isRequired(true)->radioList($model->getSearchInputTool());
}

if ($model->second_hierarchy_cd !== null) {
    if (in_array($model->table_name, SearchkeyMaster::STATIC_KEYS) || $model->principal_flg == 1) {
        echo $tableForm->row($model, 'is_category_label')->isRequired(true)->text();
    } else {
        echo $tableForm->row($model, 'is_category_label', [
            'hint' => $this->render('_hint'),
            'popoverHintOptions' => [
                'dataContainer' => '.modal-content',
                'placement' => 'right',
                'dataHtml' => true,
            ],
        ])->isRequired(true)->radioList(SearchkeyMaster::getIsCategoryLabel());
    }
}
if (in_array($model->table_name, SearchkeyMaster::ICON_STATIC_KEYS)) {
    echo $tableForm->row($model, 'icon_flg')->isRequired(true)->text();
} else {
    echo $tableForm->row($model, 'icon_flg')->isRequired(true)->radioList($model->getIconFlg());
}

if ($model->table_name == 'pref') {
    echo $tableForm->row($model, 'valid_chk')->text();
} else {
    echo $tableForm->row($model, 'valid_chk')->radioList($model->getValidArray());
}

$tableForm->endTable();
Modal::end();
TableForm::end();
Pjax::end();
