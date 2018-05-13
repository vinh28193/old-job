<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/02/10
 * Time: 16:56
 */
use app\models\manage\JobPic;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $pics \app\models\manage\JobPic[] */
/* @var $title string */
/* @var $auth string */

$finderId = $auth . 'Tag';
$filterClass = $auth . 'Pic';
$tags = JobPic::makeTagDropDownSelections($pics);

?>
    <div class="col-xs-12" style="margin-bottom:10px;">
        <h4 class="border_bottom"><?= $title ?></h4>
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 left">
                <?= Html::dropDownList(
                    'tag',
                    null,
                    $tags,
                    ['class' => 'form-control select select-simple max-w inline', 'id' => $finderId]
                ) ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 right">
                <?= Html::button(Html::icon('search') . Yii::t('app', 'このタグで絞り込む'), [
                    'class' => 'btn btn-primary mgl20 filterTagButton',
                    'data-finder' => "#{$finderId}",
                    'data-filter' => "div.{$filterClass}",
                ]) ?>
            </div>
        </div>
    </div>
<?php foreach ($pics as $pic): ?>
    <div class="col-xs-6 col-sm-3 col-md-3 <?= $filterClass ?>" data-tag="<?= $pic->tag ? Html::encode($pic->tag) : '0' ?>">
        <div class="col_box clearfix">
            <div class="col_box_inner">
                <?= Html::img($pic->srcUrl(), [
                    'data-model_id' => $pic->id,
                    'class' => 'img-responsive',
                ]) ?>
            </div>
            <?php if ($pic->client_master_id): ?>
                <div class="col_btn">
                    <?= Html::a(Html::icon('trash') . Yii::t('app', '削除する'), Url::toRoute(['delete-pic']), [
                        'title' => Yii::t('yii', 'Delete'),
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'data' => [
                            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                            'pjax' => '1',
                            'method' => 'post',
                            'params' => [
                                'id' => $pic->id,
                                'client_master_id' => $pic->client_master_id,
                            ],
                        ],
                        'class' => 'btn btn-sm btn-danger btn-delete',
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>