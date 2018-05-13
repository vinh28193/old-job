<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/07/06
 * Time: 16:38
 */

use app\common\PostablePjax;
use app\models\manage\ClientColumnSet;
use app\models\manage\JobColumnSet;
use app\models\manage\MainDisplay;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\manage\controllers\secure\settings\DisplayController;

/* @var $dispTypeId integer */
/* @var $pjaxId string */
/* @var $bothListItems JobColumnSet[] */
/* @var $bothClientItems ClientColumnSet[] */
/* @var $mainDisplayModel mainDisplay */


$form = ActiveForm::begin([
    'id' => 'form',
    'action' => 'update',
]);
PostablePjax::begin([
    'id' => DisplayController::PJAX_ID,
    'enablePushState' => false,
    'url' => Url::toRoute('list-pjax'),
    'trigger' => ['selector' => '"#dispTypeId"', 'event' => 'change'],
    'postAttribute' => 'value',
    'postAttributeName' => 'dispTypeId',
    'options' => [
        'class' => 'col-md-10 col-md-offset-1',
        'style' => 'margin-top: 20px',
    ],
]);

$js = <<<JS
$('.main-display').each(function() {
    var id = this.id;
    var input;
    if (this.length && this[0].tagName.toLowerCase() === 'div') {
        input = $(this).find('input');
    } else {
        input = $(this);
    }

    input.off();
    input.on('change.yiiActiveForm', function(e) {
        $('form').yiiActiveForm('validateAttribute', id);
    });
    input.on('blur.yiiActiveForm', function(e) {
        $('form').yiiActiveForm('validateAttribute', id);
    });
    input.on('keyup.yiiActiveForm', function(e) {
        $('form').yiiActiveForm('validateAttribute', id);
    });
});
JS;
$this->registerJs($js);

echo $this->render('_input-fields', [
    'dispTypeId' => $dispTypeId,
    'bothListItems' => $bothListItems,
    'bothClientItems' => $bothClientItems,
    'mainDisplayModel' => $mainDisplayModel,
    'form' => $form,
]);

PostablePjax::end();
ActiveForm::end();
