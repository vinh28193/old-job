<?php
/**
 * Created by PhpStorm.
 * User: mita33
 * Date: 2016/10/05
 * Time: 11:07
 */
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\models\manage\searchkey\Station;

$station = new Station();

$js = <<<JS
$("#station-station_name").on("change", function() {
    $('#selectSearchkey').text($(this).val());
});
JS;
$this->registerJs($js);
?>

<div class="col-12 clearfix mgb10 pdb10" style="border-bottom: 1px solid #eee;">
    <div class="col-md-9">
        <div class="form-group">
            <label class="control-label"><?= Yii::t('app', '駅名'); ?></label>
            <div class="form-inline">
                <?= Select2::widget([
                    'model' => $station,
                    'attribute' => 'station_name',
                    'options' => ['placeholder' => Yii::t('app', '駅を選択してください')],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 1,
                        'language' => [
                            'inputTooShort' => new JsExpression('function () {return "1文字以上入力してください";}'),
                        ],
                        'ajax' => [
                            'url' => Url::to('../../secure/job/ajax-station'),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                        ],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
