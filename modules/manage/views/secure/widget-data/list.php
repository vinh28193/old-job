<?php

use proseeds\helpers\GridHelper;
use app\models\manage\ManageMenuMain;
use proseeds\assets\BootBoxAsset;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\manage\WidgetDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
BootBoxAsset::confirmBeforeSubmit($this, Yii::t('app', '削除したものは元に戻せません。削除しますか？'), '#grid_form');
$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = $menu->title;
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="widget-data-index">
        <?php
        echo Html::tag('h1', Html::icon($menu->icon_key) . Html::encode($this->title), ['class' => 'heading']);
        echo $this->render('@vendor/proseeds/proseeds/web/_deleteComment');
        echo $this->render('_search', ['searchModel' => $searchModel]);
        echo $this->render('/secure/common/_buttons.php', [
            'pagename' => Yii::t('app', 'ウィジェットデータ'),
            'count' => $dataProvider->getTotalCount(),
            'buttons' => [
                'add' => true,
                'delete' => true,
            ],
        ]);
        Pjax::begin([]);
        if (Yii::$app->request->queryParams) {
            echo $dataProvider->getTotalCount() ? GridHelper::grid($dataProvider,
                [
                    ['type' => 'checkBox'],
                    ['type' => 'default', 'attribute' => 'widget.widget_no'],
                    ['type' => 'default', 'attribute' => 'widget.widget_name'],
                    ['type' => 'default', 'attribute' => 'title'],
                    ['type' => 'default', 'attribute' => 'description'],
                    ['type' => 'default', 'attribute' => 'disp_start_date', 'format' => 'date'],
                    ['type' => 'default', 'attribute' => 'disp_end_date', 'format' => 'date'],
                    ['type' => 'default', 'attribute' => 'valid_chk', 'format' => 'validChk'],
                    ['type' => 'operation', 'buttons' => '{update}'],
                ], $config = ['id' => 'grid_id']) : Yii::t("app", '該当するデータがありません');
        } else {
            echo Yii::t("app", '「この条件で表示する」ボタンを押せば一覧が表示されます');
        }
        Pjax::end(); ?>
    </div>
<?php
$css = <<<CSS
    .help-block{
        display :none;
    }
CSS;
$this->registerCss($css); ?>