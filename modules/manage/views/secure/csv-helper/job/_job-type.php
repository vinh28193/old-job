<?php
/**
 * Created by PhpStorm.
 * User: mita33
 * Date: 2016/10/04
 * Time: 14:39
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\manage\searchkey\JobTypeCategory;

/** @var $searchkeyMaster \app\models\manage\SearchkeyMaster */

/** @var JobTypeCategory[]|null $jobTypeCategories */
$jobTypeCategories = $searchkeyMaster->searchKeyModels;
?>
<div class="panel-group" id="type-accordion" style="overflow-y: auto; height: 480px;">

    <?php if ($jobTypeCategories): ?>
        <?php foreach ($jobTypeCategories as $jobTypeCategory): ?>
            <?php if ($jobTypeCategory->jobTypeBig): ?>
                <div class="panel panel-default">
                    <div class="panel-heading" data-toggle="collapse" data-parent="#type-accordion"
                         href="#jobTypeCategory<?= $jobTypeCategory->id ?>" aria-expanded="false"
                         aria-controls="collapseOne" style="cursor:pointer">
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
                                        if ($jobTypeBig->jobTypeSmall) {
                                            echo Html::label(Html::checkbox('selection_all', false, ['class' => 'hasChildren']) . $jobTypeBig->job_type_big_name, null, ['style' => 'margin-top: 1em;']);
                                        }
                                        echo Html::checkboxList(
                                            'JobTypeSmall',
                                            null,
                                            ArrayHelper::map($jobTypeBig->jobTypeSmall, 'job_type_small_no', 'job_type_small_name'),
                                            [
                                                'id' => 'jobType' . $jobTypeBig->id . '-' . $i,
                                                'unselect' => null,
                                                'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline dispChecked'], 'class' => 'hasParent']
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
    <?php endif; ?>
</div>
