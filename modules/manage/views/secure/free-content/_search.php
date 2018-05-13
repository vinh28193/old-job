<?php

use app\modules\manage\models\search\FreeContentSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model FreeContentSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<?php $form = ActiveForm::begin(['method' => 'get', 'action' => Url::to('list')]); ?>
    <div class="panel panel-default search-box arrow">
        <div class="container">

            <div class="row">
                <div class="search-inbox col-xs-12 col-sm-12 col-md-12">
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 title">
                            <?= $model->getAttributeLabel('valid_chk') ?>
                        </div>
                        <?= Html::activeRadioList(
                            $model,
                            'valid_chk',
                            FreeContentSearch::validArray(),
                            ['options' => 'col-xs-8 col-sm-8 col-md-8 right']
                        ) ?>
                    </div>
                </div>
            </div>
            <?= $this->render('/secure/common/_search-buttons.php', ['model' => $model]); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>