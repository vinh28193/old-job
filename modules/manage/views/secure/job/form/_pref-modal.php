<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/11
 * Time: 16:31
 */

use app\models\manage\searchkey\Pref;
use app\models\manage\SearchkeyMaster;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

/** @var \app\models\manage\JobMaster $model */
/** @var SearchkeyMaster $searchKey */

$prefCheckCss = <<<CSS
.pref-selection-label {
    margin-bottom: 0px;
    margin-top: 0px;
    height: 44px;
    position: absolute;
}
.pref-selection-label > span.icons {
    margin-top: 10px;
    margin-left: 2px;
}
.panel-title{
    padding-left: 20px;
}

#pref-accordion > div.panel {
    position: relative;
}
CSS;
$this->registerCss($prefCheckCss);

Modal::begin([
    'header' => Html::tag('h2', Yii::t('app', '勤務地を選択する')),
    'toggleButton' => ['label' => Yii::t('app', '選択する'), 'class' => 'btn btn-default'],
    'id' => 'pref',
    'size' => Modal::SIZE_LARGE,
]);
?>
    <div class="panel-group" id="pref-accordion" style="overflow-y: auto; height: 480px;">

        <?php foreach ($searchKey->searchKeyModels as $i => $pref): /** @var Pref $pref */ ?>
            <?php if ($pref->distLite): ?>
                <div class="panel panel-default">

                    <?= Html::label(Html::checkbox('selection_all', false, ['class' => ['hasChildren', 'pref-selection-checkbox'], 'data-children-wrap-class' => "pref{$pref->id}-class"]), null, ['class' => 'pref-selection-label']); ?>

                    <div class="panel-heading" data-toggle="collapse" data-parent="#pref-accordion" href="#pref<?= $pref->id ?>" aria-expanded="false" aria-controls="collapseOne" style="cursor:pointer">
                        <h4 class="panel-title">
                            <?= $pref->pref_name ?>
                        </h4>
                    </div>
                    <div id="pref<?= $pref->id ?>" class="panel-collapse collapse in" style="height: auto;">
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-xs-12">
                                    <?php echo Html::activeCheckboxList(
                                        $model->jobDistModel,
                                        'itemIds',
                                        ArrayHelper::map($pref->distLite, 'id', 'dist_name'),
                                        [
                                            'id' => 'pref' . $i,
                                            'class' => "pref{$pref->id}-class",
                                            'unselect' => null,
                                            'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline'], 'class' => 'hasParent'],
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
    <div class="modal-footer">
        <button type="button" id="prefSave" class="btn btn-primary"
                data-dismiss="modal"><?= Yii::t('app', '変更を保存') ?></button>
    </div>

<?php Modal::end(); ?>