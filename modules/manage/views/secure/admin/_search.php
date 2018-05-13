<?php

use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\models\manage\AdminMasterSearch;
use app\models\manage\AdminMaster;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $adminMasterSearch app\models\manage\AdminMasterSearch */

$listClass = ['class' => 'form-control select select-simple max-w inline'];
?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">
            <div class="row">
                <div class="search-inbox col-xs-4 col-sm-4 col-md-4">
                    <?= Html::activeDropDownList(
                        $adminMasterSearch,
                        'searchItem',
                        [$adminMasterSearch::ALL_KEY_WORD => Yii::t('app', 'すべて')] + Yii::$app->functionItemSet->admin->searchMenuAttributeLabels,
                        ['class' => 'form-control select select-info max-w inline', 'data-toggle' => 'select']
                    ) ?>
                </div>
                <div class="search-inbox col-xs-8 col-sm-8 col-md-8 right">
                    <?= Html::activeTextInput(
                        $adminMasterSearch,
                        'searchText',
                        ['placeholder' => Yii::t('app', 'キーワードを入力'), 'class' => 'form-control jq-placeholder inline',]
                    ) ?>
                </div>
            </div>

            <?= $this->render('/secure/common/_dep-drop', [
                'model' => $adminMasterSearch,
                'corpAttribute' => 'corp_master_id',
                'clientAttribute' => 'client_master_id',
                'disableSecondRow' => true,
            ]) ?>

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title"><?=Yii::t('app', '種別') ?></div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeRadioList(
                                $adminMasterSearch,
                                'role',
                                AdminMaster::getRoleList(),
                                ['class' => 'inline']); ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $adminMasterSearch->getAttributeLabel('valid_chk') ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeRadioList(
                                $adminMasterSearch,
                                'valid_chk',
                                AdminMaster::getValidChkList(),
                                [
                                    'options' => 'col-xs-8 col-sm-8 col-md-7 right',
                                    'itemOptions' => [
                                        'labelOptions' => ['class' => 'radio-inline radio inline'],
                                        'data-toggle' => 'radio'
                                    ]
                                ]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->render('/secure/common/_search-buttons.php', [
                'model' => $adminMasterSearch,
            ]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>