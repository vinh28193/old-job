<?php
use proseeds\helpers\GridHelper;
use app\models\manage\JobReviewHistory;

/** @var yii\web\View $this */
/** @var string $modal */
// 折り畳み対象判断用
$modal = $modal ?? '';
?>

<div class="panel-group" id="review-history-accordion<?= $modal ?>">
    <div class="panel panel-default">
        <div class="panel-heading" data-toggle="collapse" data-parent="#review-history-accordion<?= $modal ?>" href="#collapseOne<?= $modal ?>">
            <span class="pull-right glyphicon glyphicon-chevron-down mod-h2" style="top: 5px;"></span>
            <h2 class="panel-title mod-h2 pdl0" style="clear: none;">
                <?= Yii::t('app', '審査履歴') ?>
                <span class="text-red ft13"><?= Yii::t('app', '※最新{max}件を表示', ['max' => JobReviewHistory::HISTORY_MAX]); ?></span>
            </h2>

        </div>
        <div id="collapseOne<?= $modal ?>" class="panel-collapse collapse">
            <?= GridHelper::grid(
                JobReviewHistory::dataProvier($id),
                JobReviewHistory::listItems(),
                [
                    'layout' => '{items}',
                    'tableOptions' => [
                        'class' => 'table table-striped table-bordered mgb0',
                    ],
                ]
            );
            ?>
        </div>
    </div>
</div>
