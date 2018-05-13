<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/04/22
 * Time: 16:33
 */

use app\common\CustomEditable;
use app\models\manage\JobMaster;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $mainDisps \app\models\manage\JobColumnSet[] */
/* @var $model JobMaster */
/* @var $picId Integer */

$picTextName = 'pic' . $picId . '_text';
?>

<?php if (isset($mainDisps['pic' . $picId]) || isset($mainDisps[$picTextName])) : ?>
    <li class="mod-slider__item">
        <?php if (isset($mainDisps['pic' . $picId])) : ?>
            <div class="img">
                <?= Html::img($model->getJobImagePath($picId) ?: $model::NO_IMAGE_PATH, [
                    'id' => $mainDisps['pic' . $picId]->column_name,
                    'class' => 'mod-slider__image',
                    'style' => 'cursor:pointer',
                ]); ?>
            </div>
        <?php endif; ?>
        <?php
        if (isset($mainDisps[$picTextName])) {
            echo CustomEditable::widget([
                'model' => $model,
                'attribute' => $mainDisps[$picTextName]->column_name,
                'type' => 'textarea',
                'tag' => 'p',
                'options' => [
                    'id' => 'main-' . $mainDisps[$picTextName]->column_name,
                    'class' => 'mod-slider__excerpt',
                    'style' => 'cursor:pointer',
                ],
                'maxLength' => $mainDisps[$picTextName]->max_length,
                'clientOptions' => [
                    'rows' => 1,
                    'emptytext' => $mainDisps[$picTextName]->label,
                    'tpl' => '<textarea style="width:100%;"></textarea>',
                ],
            ]);
        }
        ?>
    </li>
<?php endif;
