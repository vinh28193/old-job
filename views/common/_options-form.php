<?php

use app\common\widget\FormattedDatePicker;
use app\models\manage\ApplicationColumnSet;
use app\models\manage\BaseColumnSet;
use yii\helpers\ArrayHelper;

//オプション項目の共通ビュー
/* @var $kyujinForm \app\common\KyujinForm*/
/* @var $columnSetModel ApplicationColumnSet ○○_column_set系のモデル */
/* @var $model \yii\db\ActiveRecord 表示するデータのモデルクラス */

//インプットテキストのオプション
switch ($columnSetModel->data_type) {
    case BaseColumnSet::DATA_TYPE_NUMBER:
    case BaseColumnSet::DATA_TYPE_URL:
    case BaseColumnSet::DATA_TYPE_TEXT:
        echo $kyujinForm->row($model, $columnSetModel->column_name)->textarea(['placeholder' => $columnSetModel->column_explain]);
        break;
    case BaseColumnSet::DATA_TYPE_MAIL:
        echo $kyujinForm->row($model, $columnSetModel->column_name)->textInput(['placeholder' => $columnSetModel->column_explain]);
        break;
    case BaseColumnSet::DATA_TYPE_DROP_DOWN:
        echo $kyujinForm->row($model, $columnSetModel->column_name)->dropDownList(
            ArrayHelper::map($columnSetModel->subsetItems, 'subset_name', 'subset_name'),
            ['prompt' =>Yii::t('app', '--選択してください--')]
        );
        break;
    case BaseColumnSet::DATA_TYPE_CHECK:
        echo $kyujinForm->row($model, $columnSetModel->column_name)->checkboxList(ArrayHelper::map($columnSetModel->subsetItems, 'subset_name', 'subset_name'), ['class' => 'mod-form1 inline-checkbox', 'tag' => 'ul']);
        break;
    case BaseColumnSet::DATA_TYPE_DATE: // 今のところ使われることは無い
        echo $kyujinForm->row($model, $columnSetModel->column_name)->widget(FormattedDatePicker::className());
        break;
    case BaseColumnSet::DATA_TYPE_RADIO:
        echo $kyujinForm->row($model, $columnSetModel->column_name)->radioList(ArrayHelper::map($columnSetModel->subsetItems, 'subset_name', 'subset_name'), ['class' => 'mod-form1 inline-radio', 'tag' => 'ul']);
        break;
    default:
        echo $kyujinForm->row($model, $columnSetModel->column_name)->textarea(['rows' => $columnSetModel->rows, 'class' => 'form-control']);
        break;
}