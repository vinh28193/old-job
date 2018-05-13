<?php

use kartik\widgets\DepDrop;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\manage\ClientMaster;
use app\models\manage\ClientChargePlan;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $clientMasterSearch app\models\manage\clientMasterSearch */

$listClass = ['class' => 'form-control select select-simple max-w inline'];
$hiddenListClass = ['class' => 'form-control select select-simple max-w inline hidden'];
?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">
            <div class="row">
                <div class="search-inbox col-xs-4 col-sm-4 col-md-4">
                    <?= Html::activeDropDownList($clientMasterSearch, 'searchItem', ['all' => 'すべて'] + Yii::$app->functionItemSet->client->searchMenuAttributeLabels, ['class' => 'form-control select select-info max-w inline', 'data-toggle' => 'select']) //todo 権限による項目フィルタ   ?>
                </div>
                <div class="search-inbox col-xs-8 col-sm-8 col-md-8 right">
                    <?= Html::activeTextInput($clientMasterSearch, 'searchText', ['placeholder' => 'キーワードを入力', 'class' => 'form-control jq-placeholder inline',]) ?>
                </div>
            </div>
            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $clientMasterSearch->getAttributeLabel('clientChargeType') ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeDropDownList($clientMasterSearch, 'clientChargeType', ['' => Yii::t('app', 'すべて')] + ClientChargePlan::getChargeTypeArray(), $listClass) ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $clientMasterSearch->getAttributeLabel('clientChargePlanId')?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= DepDrop::widget([
                                'model' => $clientMasterSearch,
                                'attribute' => 'clientChargePlanId',
                                'type' => DepDrop::TYPE_DEFAULT,
                                'options' => ['class' => 'form-control select select-simple max-w'],
                                'pluginOptions' => [
                                    'depends' => [Html::getInputId($clientMasterSearch, 'clientChargeType')],
                                    'url' => Url::to(['plan-list-search']),
                                    'placeholder' => Yii::t('app', 'すべて'),
                                    'initialize' => true,
                                ],
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title"><?= Yii::t('app', '取引状態') ?></div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeRadioList($clientMasterSearch, 'valid_chk', ClientMaster::getValidChkList(), [
                                'options' => 'col-xs-8 col-sm-8 col-md-7 right',
                                'itemOptions' => [
                                    'labelOptions' => ['class' => 'radio-inline radio inline'],
                                    'data-toggle' => 'radio'
                                ]
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->render('/secure/common/_search-buttons.php', [
                'model' => $clientMasterSearch,
            ]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>