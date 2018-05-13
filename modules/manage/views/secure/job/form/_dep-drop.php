<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/07/25
 * Time: 9:49
 */
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\JobMaster;
use app\modules\manage\models\Manager;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\manage\JobMaster */
/* @var $tableForm \proseeds\widgets\TableForm */
/* @var $inputs array */
/* @var $items \app\common\ColumnSet */
/** @var Manager $identity */
$identity = Yii::$app->user->identity;
switch (ArrayHelper::getValue($inputs, 'corp')) {
    case 'input':
        $tooShort = Yii::t('app', '1文字以上入力してください');
        echo $tableForm->row($model, 'corpMasterId')->widget(Select2::className(), [
            'model' => $model,
            'attribute' => 'corpMasterId',
            'initValueText' => isset($model->corpMasterId) ? $model->clientMaster->corpMaster->corp_name : Yii::t('app', 'すべて'),
            'options' => [
                'placeholder' => Yii::t('app', 'すべて'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'language' => ['inputTooShort' => new JsExpression("function () {return '{$tooShort}'; }"),],
                'ajax' => [
                    'url' => Url::to('corp-list'),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                ],
            ],
        ])->hint($items['corpLabel']->explain);
        break;
    case 'text':
        echo $tableForm->field($model, 'corpMasterId')->layout(function () use ($identity) {
            echo $identity->corpMaster->corp_name;
        });
        echo $tableForm->breakLine();
        break;
    default:
        break;
}

switch (ArrayHelper::getValue($inputs, 'client')) {
    case 'input':
        if (ArrayHelper::getValue($inputs, 'corp') == 'input') {
            // corpもinputの時はDepDrop
            echo $tableForm->row($model, 'client_master_id')->isRequired(true)->widget(DepDrop::className(), [
                'type' => DepDrop::TYPE_SELECT2,
                'data' => ClientMaster::getDropDownArray(false, JobMaster::FLAG_VALID, $model->corpMasterId ?: false),
                'pluginOptions' => [
                    'depends' => [Html::getInputId($model, 'corpMasterId')],
                    'url' => Url::to(['client-list']),
                    'placeholder' => Yii::t('app', '--選択してください--'),
                ],
            ])->hint($items['client_master_id']->explain);
        } else {
            // その他の時はidentityのcorp_master_idで絞って表示
            echo $tableForm->row($model, 'client_master_id')->isRequired(true)->widget(Select2::className(), [
                'data' => ClientMaster::getDropDownArray(Yii::t('app', '--選択してください--'), JobMaster::FLAG_VALID, $identity->corp_master_id),
            ])->hint($items['client_master_id']->explain);
        }
        break;
    case 'text':
        // relationがあればそれを、無ければidentityを表示
        if ($model->clientMaster) {
            echo $tableForm->field($model->clientMaster, 'client_name')->text();
            // 原稿画像登録モーダルの、アップロード画像一覧の表示の制限に掲載企業IDが必要なため追加している（既存更新の場合、リレーションから取得）。
            echo \yii\helpers\Html::activeHiddenInput($model, 'client_master_id');
            echo $tableForm->breakLine();
        } else {
            echo $tableForm->field($model, 'client_master_id')->layout(function () use ($identity, $model) {
                echo $identity->clientMaster->client_name;
            });
            // 原稿画像登録モーダルの、アップロード画像一覧の表示の制限に掲載企業IDが必要なため追加している（新規登録の場合、Identityから取得）。
            echo \yii\helpers\Html::activeHiddenInput(
                $model,
                'client_master_id',
                ['value' => $identity->client_master_id]
            );
            echo $tableForm->breakLine();
        }
        break;
    default:
        break;
}
switch (ArrayHelper::getValue($inputs, 'plan')) {
    case 'input':
        if (ArrayHelper::getValue($inputs, 'client') == 'input') {
            // clientもinputの時はDepDrop
            echo $tableForm->row($model, 'client_charge_plan_id', ['enableAjaxValidation' => true])->widget(DepDrop::className(), [
                'type' => DepDrop::TYPE_DEFAULT,
                'data' => ClientChargePlan::getDropDownArray(false, $model->client_master_id ?: false),
                'pluginOptions' => [
                    'depends' => ['jobmaster-client_master_id'],
                    'url' => Url::to(['plan-list']),
                    'placeholder' => false,
                ],
            ])->hint($items['client_charge_plan_id']->explain);
        } else {
            // その他の時はidentityのclient_master_idで絞って表示
            echo $tableForm->row($model, 'client_charge_plan_id', ['enableAjaxValidation' => true])
                ->dropDownList(ClientChargePlan::getDropDownArray(Yii::t('app', '--選択してください--'), $identity->client_master_id))
                ->hint($items['client_charge_plan_id']->explain);
        }
        break;
    case 'text':
        echo $tableForm->row($model->clientChargePlan, 'plan_name')->layout(function () use ($model, $tableForm) {
            echo $model->clientChargePlan->plan_name;
            echo $tableForm->form($model, 'client_charge_plan_id')->hiddenInput();
        });
        echo $tableForm->breakLine();
        break;
    default:
        break;
}