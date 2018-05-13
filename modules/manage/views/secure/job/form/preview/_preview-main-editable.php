<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/04/27
 * Time: 19:56
 */

use app\common\CustomEditable;
use app\models\manage\BaseColumnSet;
use app\models\manage\JobMaster;
use app\models\manage\MainDisp;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model JobMaster */
/* @var $mainDisps \app\models\manage\JobColumnSet[] */
/* @var $mainDispName string */

if (isset($mainDisps[$mainDispName])) {
    $tagInfo = ArrayHelper::getValue(MainDisp::TAG_INFO, $mainDispName);
    $options = ['id' => 'main-' . $mainDisps[$mainDispName]->column_name];
    Html::addCssClass($options, ArrayHelper::getValue($tagInfo, 'options', ''));

    echo Html::a('', null, [
        'type' => 'button',
        'data-toggle' => 'tooltip',
        'data-placement' => 'top',
        'title' => $mainDisps[$mainDispName]->label,
    ]);

    $editableConfig = [
        'model' => $model,
        'attribute' => $mainDisps[$mainDispName]->column_name,
        'tag' => ArrayHelper::getValue($tagInfo, 'tag'),
        'options' => $options,
        'maxLength' => $mainDisps[$mainDispName]->max_length,
        'clientOptions' => [
            'emptytext' => $mainDisps[$mainDispName]->label,
        ],
        'hint' => $mainDisps[$mainDispName]->explain,
    ];
    switch ($mainDisps[$mainDispName]->data_type) {
        case BaseColumnSet::DATA_TYPE_TEXT:
            $editableConfig['type'] = 'textarea';
            $editableConfig['clientOptions'] = [
                'rows' => 1,
                'emptytext' => $mainDisps[$mainDispName]->label,
                'tpl' => '<textarea style="width:100%;"></textarea>',
            ];
            break;
        case BaseColumnSet::DATA_TYPE_NUMBER:
            $editableConfig['countType'] = 'number';
            break;
        default:
            break;
    }
    echo CustomEditable::widget($editableConfig);
}