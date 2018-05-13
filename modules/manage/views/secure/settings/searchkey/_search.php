<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\SearchkeyMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-6 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $searchModel->getAttributeLabel('is_on_top'); ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeDropDownList(
                                $searchModel,
                                'is_on_top',
                                $searchModel->getIsOnTop(),
                                ['class' => 'form-control jq-placeholder', 'prompt' => Yii::t('app', 'すべて')]
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="search-inbox col-xs-12 col-sm-6 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $searchModel->getAttributeLabel('hierarchyType'); ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeDropDownList(
                                $searchModel,
                                'hierarchyType',
                                $searchModel->getHierarchyType(),
                                ['class' => 'form-control jq-placeholder', 'prompt' => Yii::t('app', 'すべて')]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $searchModel->getAttributeLabel('valid_chk'); ?>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeRadioList(
                                $searchModel,
                                'valid_chk',
                                $searchModel->getValidArray(),
                                ['options' => 'col-xs-8 col-sm-8 col-md-8 right',]
                            ) ?>
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