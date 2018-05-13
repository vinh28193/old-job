<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/11
 * Time: 15:30
 */

use app\models\manage\searchkey\JobTypeCategory;
use app\models\manage\SearchkeyMaster;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/** @var \app\models\manage\JobMaster $model */
/** @var SearchkeyMaster $searchKey */

Modal::begin([
    'header' => Html::tag('h2', Yii::t('app', '職種を選択する')),
    'toggleButton' => ['label' => Yii::t('app', '選択する'), 'class' => 'btn btn-default'],
    'id' => 'jobType',
    'size' => Modal::SIZE_LARGE,
]);
?>
    <div class="panel-group" id="type-accordion" style="overflow-y: auto; height: 480px;">

        <?php foreach ($searchKey->searchKeyModels as $jobTypeCategory): /** @var JobTypeCategory $jobTypeCategory */ ?>
            <?php if ($jobTypeCategory->jobTypeBig): ?>
                <div class="panel panel-default">
                    <div class="panel-heading" data-toggle="collapse" data-parent="#type-accordion" href="#jobTypeCategory<?= $jobTypeCategory->id ?>" aria-expanded="false" aria-controls="collapseOne" style="cursor:pointer">
                        <h4 class="panel-title">
                            <?= $jobTypeCategory->name ?>
                        </h4>
                    </div>
                    <div id="jobTypeCategory<?= $jobTypeCategory->id ?>" class="panel-collapse collapse in"
                         style="height: auto;">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php
                                    foreach ($jobTypeCategory->jobTypeBig as $i => $jobTypeBig) {
                                        /** @var \app\models\manage\searchkey\JobTypeBig $jobTypeBig */
                                        if ($jobTypeBig->jobTypeSmall) {
                                            echo Html::label(Html::checkbox('selection_all', false, ['class' => 'hasChildren']) . $jobTypeBig->job_type_big_name, null, ['style' => 'margin-top: 1em;']);
                                        }
                                        echo Html::activeCheckboxList(
                                            $model->jobTypeModel,
                                            'itemIds',
                                            ArrayHelper::map($jobTypeBig->jobTypeSmall, 'id', 'job_type_small_name'),
                                            [
                                                'id' => 'jobType' . $jobTypeBig->id . '-' . $i,
                                                'unselect' => null,
                                                'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline'], 'class' => 'hasParent']
                                            ]
                                        );
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="modal-footer">
        <button type="button" id="jobTypeSave" class="btn btn-primary" data-dismiss="modal"><?= Yii::t('app', '変更を保存') ?></button>
    </div>

<?php Modal::end(); ?>