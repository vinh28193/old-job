<?php
/**
 * Created by PhpStorm.
 * User: mita33
 * Date: 2016/10/01
 * Time: 15:42
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\manage\searchkey\Pref;
use app\models\manage\SearchkeyMaster;

/** @var $searchkeyMaster \app\models\manage\SearchkeyMaster */

/** @var $searchKeyCategories \app\models\manage\searchkey\SearchkeyCategory[]|null */
$prefs = $searchkeyMaster->searchKeyModels;
?>
<div class="panel-group" id="pref-accordion" style="overflow-y: auto; height: 480px;">

    <?php foreach ($prefs as $i => $pref): /** @var Pref $pref */ ?>
        <?php if ($pref->distLite): ?>
            <div class="panel panel-default">
                <div class="panel-heading" data-toggle="collapse" data-parent="#pref-accordion" href="#pref<?= $pref->id ?>" aria-expanded="false" aria-controls="collapseOne" style="cursor:pointer">
                    <h4 class="panel-title">
                        <?= $pref->pref_name ?>
                    </h4>
                </div>
                <div id="pref<?= $pref->id ?>" class="panel-collapse collapse in" style="height: auto;">
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <?php echo Html::checkboxList(
                                    'Dist',
                                    null,
                                    ArrayHelper::map($pref->distLite, 'dist_cd', 'dist_name'),
                                    [
                                        'unselect' => null,
                                        'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline dispChecked']],
                                    ]
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>