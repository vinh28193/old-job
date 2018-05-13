<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $corpMasterSearch \app\models\manage\CorpMasterSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title_search">
                            <?= Html::activeDropDownList(
                                $corpMasterSearch,
                                'searchItem',
                                ['all' => Yii::t('app', 'すべて')] + Yii::$app->functionItemSet->corp->searchMenuAttributeLabels,
                                ['class' => 'form-control select select-info max-w', 'data-toggle' => 'select']
                            ) ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeTextInput(
                                $corpMasterSearch,
                                'searchText',
                                ['placeholder' => Yii::t('app', 'キーワードを入力'), 'class' => 'form-control jq-placeholder',]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
            <?php // 審査機能ONのときのみ ?>
            <?php if (Yii::$app->tenant->tenant->review_use) : ?>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $corpMasterSearch->getAttributeLabel('corp_review_flg') ?>
                        </div>
                        <?= Html::activeRadioList(
                            $corpMasterSearch,
                            'corp_review_flg',
                            ArrayHelper::getValue($corpMasterSearch->formatTable, 'corp_review_flg'),
                            ['options' => 'col-xs-8 col-sm-8 col-md-8 right',]
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-<?= Yii::$app->tenant->tenant->review_use ? 6 : 12 ?>">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $corpMasterSearch->getAttributeLabel('valid_chk') ?>
                        </div>
                        <?= Html::activeRadioList(
                            $corpMasterSearch,
                            'valid_chk',
                            ArrayHelper::getValue($corpMasterSearch->formatTable, 'valid_chk'),
                            ['options' => 'col-xs-8 col-sm-8 col-md-8 right',]
                        ) ?>
                    </div>
                </div>
            </div>
            <?= $this->render('/secure/common/_search-buttons.php', [
                'model' => $corpMasterSearch,
            ]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>