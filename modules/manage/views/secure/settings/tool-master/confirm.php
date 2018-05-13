<?php
/**
 * Created by PhpStorm.
 * User: KNakamoto
 * Date: 2016/02/18
 * Time: 19:10
 */

/* @var \app\common\csv\CsvDataProvider $dataProvider */

use app\models\manage\ManageMenuMain;
use proseeds\helpers\GridHelper;
use proseeds\widgets\grid\ProseedsGridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Alert;

$menu = ManageMenuMain::findFromRoute('manage/secure/settings/tool-master/index');
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="tool-master-index">
        <?= Html::tag('h1', Html::icon($menu->icon_key) . Html::encode($this->title), ['class' => 'heading']) ?>

        <div class="col-md-12" role="complementary">
            <?php
            echo Alert::widget([
                'body' => Html::tag("b", Yii::t("app", "以下の内容で登録してもよろしいですか？（まだ登録は完了していません。）")),
                'closeButton' => false,
                'options' => [
                    'class' => "alert alert-warning",
                    'role' => 'alert',
                ],
            ]);
            echo Html::beginForm(Url::to(['register']), 'post', ['class' => 'form-inline']);
            echo Html::tag('p', implode(PHP_EOL, [
                Html::a("戻る", Url::to(["index"]), ['class' => 'btn btn-default']),
                Html::submitButton(Yii::t("app", "登録する"), ['class' => 'btn btn-lg btn-primary']),
            ]), ['align' => 'center', 'style' => Html::cssStyleFromArray(['margin-bottom' => '20px'])]);
            echo Html::hiddenInput("filename", $filename);
            echo Html::endForm();

            \yii\widgets\Pjax::begin();
            echo ProseedsGridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    GridHelper::dataColumn(['attribute' => 'tool_no']),
                    GridHelper::dataColumn(['attribute' => 'page_name']),
                    GridHelper::dataColumn(['attribute' => 'title']),
                    GridHelper::dataColumn(['attribute' => 'description']),
                    GridHelper::dataColumn(['attribute' => 'keywords']),
                    GridHelper::dataColumn(['attribute' => 'h1']),
                ],
                'renderCheckCount' => false,
                'limitCaution' => Yii::t('app', '{count}件のユーザーがCSVから読み込まれました。1000件以上は一覧に表示されません。', ['count' => $dataProvider->getTotalCount()]),
            ]);
            \yii\widgets\Pjax::end();
            ?>
        </div>
</div>
