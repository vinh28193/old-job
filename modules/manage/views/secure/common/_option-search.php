<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\FunctionItemSetSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin(['method' => 'get']); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            項目名
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 right">
                            <?= Html::activeTextInput(
                                $searchModel,
                                'searchText',
                                ['placeholder' => 'キーワードを入力', 'class' => 'form-control jq-placeholder',]
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
            
                    <?= $this->render('/secure/common/_search-buttons.php', ['model' => $searchModel]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>