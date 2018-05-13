<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\CustomFieldSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">
            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
                    <div class="row">
                        <div class="col-xs-1 col-sm-1 col-md-1 title">
                            <?= $searchModel->getAttributeLabel('url'); ?>
                        </div>
                        <div class="col-xs-11 col-sm-11 col-md-11 right">
                            <?= Html::activeTextInput(
                                $searchModel,
                                'url',
                                ['class' => 'form-control jq-placeholder', 'placeholder' => Yii::t('app', 'キーワードを入力')]
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