<?php
use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\SendMailSetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;;
?>
<div class="policy-index">
    <?php echo Html::tag('h1', Html::icon($menu->icon_key) . Yii::t('app', '{title}設定', ['title' => Html::encode($this->title)]), ['class' => 'heading']) ?>
    <?php echo $this->render('_search', ['searchModel' => $searchModel]); ?>

    <?= $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider, [
        ['type' => 'number'],
        ['type' => 'default', 'attribute' => 'policy_name'],
        ['type' => 'default', 'attribute' => 'description'],
        ['type' => 'default', 'attribute' => 'url', 'format' => 'newWindowUrl', 'usePopover' => false],
        ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'isPublished', 'headerClass' => 'ss-column'],
        ['type' => 'operation', 'buttons' => '{update} {preview}'],
    ], ['layout' => "<div class='table-wrap'>{items}</div>"]) : Yii::t('app', '該当するデータがありません') ?>
</div>
