<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\manage\SendMailSetSearch;
use app\models\manage\SendMailSet;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\SendMailSetSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">
            <div class="row">

                <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
                    <div class="search-inbox col-xs-4 col-sm-4 col-md-4">
                        <?=
                        Html::activeDropDownList(
                            $searchModel,
                            'searchItem',
                            ArrayHelper::merge($searchModel->getDefaultSelectLabel(), $searchModel->itemSearchs()),
                            ['class' => 'form-control select select-info max-w inline', 'data-toggle' => 'select']
                        )
                        ?>
                    </div>
                    <div class="search-inbox col-xs-8 col-sm-8 col-md-8 right">
                        <?=
                        Html::activeTextInput(
                            $searchModel,
                            'searchText',
                            ['placeholder' => Yii::t('app', 'キーワードを入力'), 'class' => 'form-control jq-placeholder inline']
                        )
                        ?>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
                    <div class="search-inbox col-xs-12 col-sm-6 col-md-6">
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4 title">
                                <?= $searchModel->getAttributeLabel('mail_name'); ?>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8 right">
                                <?= Html::activeDropDownList(
                                    $searchModel,
                                    'mail_name',
                                    ArrayHelper::map(SendMailSet::find()->where(['valid_chk' => SendMailSet::VALID])->all(), 'mail_name', 'mail_name'),
                                    ['class' => 'form-control jq-placeholder', 'prompt' => Yii::t('app', 'すべて')]
                                ) ?>
                            </div>
                        </div>
                    </div>
                    <div class="search-inbox col-xs-12 col-sm-6 col-md-6">
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4 title">
                                <?= $searchModel->getAttributeLabel('mail_to'); ?>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8 right">
                                <?= Html::activeDropDownList(
                                    $searchModel,
                                    'mail_to',
                                    $searchModel->getMailToLabel(),
                                    ['class' => 'form-control jq-placeholder', 'prompt' => Yii::t('app', 'すべて')]
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->render('/secure/common/_search-buttons.php', [
                'model' => $searchModel,
            ]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>