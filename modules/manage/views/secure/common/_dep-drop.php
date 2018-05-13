<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/04/30
 * Time: 13:36
 */

use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\modules\manage\models\Manager;
use kartik\depdrop\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use proseeds\widgets\PopoverWidget;

/* @var $model \yii\db\ActiveRecord */
/* @var $corpAttribute string */
/* @var $clientAttribute string */
/* @var $clientChargePlanAttribute string */
/* @var $theOtherLabel string */
/* @var $theOtherInput string */
/* @var $popoverLabel string */

/** @var Manager $identity */
$identity = Yii::$app->user->identity;

?>
    <div class="row">
        <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
            <div class="row">
                <?php if (!isset($popoverLabel)): ?>
                    <div class="col-xs-4 col-sm-4 col-md-4 title">
                        <?= Html::activeLabel($model, $corpAttribute); ?>
                    </div>
                <?php else : ?>
                    <div class="col-xs-4 col-sm-4 col-md-4 title col-popover-title">
                        <?= Html::activeLabel($model, $corpAttribute); ?>
                    </div>
                    <div class="col-popover">
                        <?= PopoverWidget::widget(['dataContent' => $popoverLabel]) ?>
                    </div>
                <?php endif; ?>
                <div class="col-xs-8 col-sm-8 col-md-8 right">
                    <?php
                    switch ($identity->myRole) {
                        case Manager::OWNER_ADMIN:
                            // todo 件数（に応じてtenantテーブル等で切り替えるスイッチ）によって一文字入力必須か否かを切り替えられるように
                            echo Select2::widget([
                                'model' => $model,
                                'attribute' => $corpAttribute,
                                'initValueText' => ($corp = CorpMaster::findOne($model->$corpAttribute)) ? $corp->corp_name : 'すべて',
                                'options' => [
                                    'placeholder' => Yii::t('app', 'すべて'),
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 1,
                                    'language' => [
                                        'inputTooShort' => new JsExpression('function () {return "' . Yii::t('app', '1文字以上入力してください') . '";}'),
                                        'errorLoading' => new JsExpression('function () {return "' . Yii::t('app', '読み込み中…') . '";}'),
                                        'noResults' => new JsExpression('function () {return "' . Yii::t('app', '対象が見つかりません') . '";}'),
                                    ],
                                    'ajax' => [
                                        'url' => Url::to('corp-list-search'),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                    ],
                                ],
                            ]);
                            break;
                        case Manager::CORP_ADMIN:
                        case Manager::CLIENT_ADMIN:
                            echo Html::tag('p',null,['class' => 'mgt8']);
                            echo $identity->corpMaster->corp_name;
                            echo Html::activeHiddenInput($model, $corpAttribute, ['value' => $identity->corp_master_id]);
                            break;
                        default :
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
            <div class="row">
                <?php if (!isset($popoverLabel)): ?>
                    <div class="col-xs-4 col-sm-4 col-md-4 title">
                        <?= Html::activeLabel($model, $clientAttribute); ?>
                    </div>
                <?php else : ?>
                    <div class="col-xs-4 col-sm-4 col-md-4 title col-popover-title">
                        <?= Html::activeLabel($model, $clientAttribute); ?>
                    </div>
                    <div class="col-popover">
                        <?= PopoverWidget::widget(['dataContent' => $popoverLabel]) ?>
                    </div>
                <?php endif; ?>
                <div class="col-xs-8 col-sm-8 col-md-8 right">
                    <?php
                    switch ($identity->myRole) {
                        case Manager::OWNER_ADMIN:
                        case Manager::CORP_ADMIN:
                            echo DepDrop::widget([
                                'model' => $model,
                                'attribute' => $clientAttribute,
                                'type' => DepDrop::TYPE_SELECT2,
                                'data' => ClientMaster::getDropDownArray(Yii::t('app', 'すべて'), 1, $model->$corpAttribute),
                                'pluginOptions' => [
                                    'depends' => [Html::getInputId($model, $corpAttribute)],
                                    'url' => Url::to(['client-list-search']),
                                    'placeholder' => Yii::t('app', 'すべて'),
                                    'initialize' => true,
                                ],
                                'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                            ]);
                            break;
                        case Manager::CLIENT_ADMIN:
                            echo Html::tag('p',null,['class' => 'mgt8']);
                            echo $identity->clientMaster->client_name;
                            echo Html::activeHiddenInput($model, $clientAttribute, ['value' => $identity->client_master_id]);
                            break;
                        default :
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

<?php if (!isset($disableSecondRow)): ?>
    <?php
    $planInputId = Html::getInputId($model, $clientChargePlanAttribute);

    $setSelectedPlanJs = <<<JS
$('#{$planInputId}').change(function(){
    $('#oldClientChargePlanId').val($(this).val());
})
JS;

    $this->registerJs($setSelectedPlanJs);
    ?>
    <div class="row">
        <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 title">
                    <?= Html::activeLabel($model, $clientChargePlanAttribute); ?>
                </div>
                <div class="col-xs-8 col-sm-8 col-md-8 right">
                    <?= Html::hiddenInput('oldClientChargePlanId', $model->$clientAttribute ? '' : $model->$clientChargePlanAttribute, ['id' => 'oldClientChargePlanId']) ?>
                    <?= DepDrop::widget([
                        'model' => $model,
                        'attribute' => $clientChargePlanAttribute,
                        'type' => DepDrop::TYPE_DEFAULT,
                        'options' => ['class' => 'form-control select select-simple max-w'],
                        'data' => ClientChargePlan::getDropDownArray(Yii::t('app', 'すべて'), $model->$clientAttribute),
                        'pluginOptions' => [
                            'depends' => [Html::getInputId($model, $clientAttribute)],
                            'url' => Url::to(['plan-list-search']),
                            'placeholder' => Yii::t('app', 'すべて'),
                            'initialize' => true,
                            'params' => ['oldClientChargePlanId']
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
        <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
            <div class="row">
                <div class="col-xs-4 col-sm-4 col-md-4 title">
                    <?= $theOtherLabel ?>
                </div>
                <div class="col-xs-8 col-sm-8 col-md-8 right">
                    <?= $theOtherInput ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>