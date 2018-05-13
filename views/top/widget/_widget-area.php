<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\manage\searchkey\Area;

/* @var $this yii\web\View */
/* @var $searchForm \app\models\forms\JobSearchForm */
/* @var $areaId integer */

$areaButtonOption = [
    'class' => 'mod-btn7'
];
$areaCount = count($searchForm->areas);
if ($areaCount >= 7) {
    Html::addCssClass($areaButtonOption,"mod-btn7__x-small");
} elseif ($areaCount >= 5) {
    Html::addCssClass($areaButtonOption,"mod-btn7__small");
} elseif ($areaCount >= 3) {
    Html::addCssClass($areaButtonOption,"mod-btn7__large");
} else {
    Html::addCssClass($areaButtonOption,"mod-btn7__x-large");
}
?>
<?php if ($areaId == Area::NATIONWIDE_ID && count($searchForm->areas) > 1): ?>
    <div class="widget widget_zenkoku">
        <h2><span class="fa fa-search"></span><?= Yii::t('app', ' 勤務地で探す') ?></h2>
        <div class="widget-inner">
            <div class="btn-group">
                <ul class="btn-group__center">
                    <?php foreach ($searchForm->areas as $area) : ?>
                        <li>
                            <?=Html::a(Html::encode($area->area_name),$area->area_dir,$areaButtonOption)?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>