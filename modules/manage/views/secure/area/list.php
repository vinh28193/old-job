<?php

use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;
use app\models\manage\SearchkeyMaster;
use kartik\sortinput\SortableInput;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $areas Area[] */


$this->title = SearchkeyMaster::findName($areas[0]->tableName())->searchkey_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <h1 class="heading"><?= Html::icon('search') . Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-md-9">
            <p class="alert alert-warning"><?= Yii::t('app', 'ドラッグ＆ドロップで割り当て及び並び替え（左から昇順）ができます。使用しない都道府県は右側に移動してください。<br />Top画面及び検索画面の都道府県選択ヵ所のみ並び替えられ、管理画面や応募画面の都道府県の並び順には影響しません。') ?></p>

            <?= Yii::$app->session->getFlash('operationComment') ?>

            <?php
            ActiveForm::begin(['action' => 'sort-update']);
            $areaItems = [];
            foreach ($areas as $area) {
                // 有効か無効かで切り分け
                if ($area->valid_chk) {
                    $label = '<span class="label label-success" style="margin-right:5px;">公開中</span>';
                    $options = [];
                } else {
                    $label = '<span class="label label-default" style="margin-right:5px;">非公開</span>';
                    $options = ['style' => 'background-color:#EDEDED;'];
                }
                // エリア名
                $areaName = Html::a($area->area_name, Url::to(['pjax-modal', 'areaId' => $area->id]), ['class' => 'pjaxModal', 'title' => Yii::t('app', '変更')]);

                // 都道府県のsort要素の生成
                $prefItems = [];
                foreach ($area->pref as $pref) {
                    $prefItems[$pref->id] = ['content' => Html::a($pref->pref_name, Url::toRoute(['/manage/secure/prefdist/list', 'PrefDistMaster[pref_id]' => $pref->id]))];
                }
                $prefSort = SortableInput::widget([
                    'name' => "prefIds[{$area->id}]",
                    'items' => $prefItems,
                    'sortableOptions' => [
                        'itemOptions' => ['class' => 'btn btn-simple ui-sortable-handle'],
                        'connected' => true,
                    ],
                    'options' => ['class' => 'form-control', 'readonly' => true]
                ]);


                $content = "<div class=\"row\"><div class=\"search-inbox col-xs-2 col-sm-2 col-md-2\">{$areaName} {$label}</div><div class=\"search-inbox col-xs-10 col-sm-10 col-md-10 right\">{$prefSort}</div></div>";
                $areaItems['area' . $area->id] = ['content' => $content, 'options' => $options];
            }

            echo SortableInput::widget([
                'name' => "Areas",
                'items' => $areaItems,
                'sortableOptions' => [
                    'itemOptions' => ['class' => ''],
                    'connected' => false,
                ],
                'options' => ['class' => 'form-control', 'readonly' => true]
            ]);
            ?>
        </div>

        <div class="col-md-3" id="fixedPoint">
            <div id="fixedBox" data-spy="affix" class="affix">
                <h3><?= Yii::t('app', '使用しない都道府県') ?></h3>
                <?php
                $items = [];
                foreach (Pref::find()->where(['area_id' => null])->all() as $pref) {
                    /** @var Pref $pref */
                    $items[$pref->id] = ['content' => Html::a($pref->pref_name, Url::toRoute(['/manage/secure/prefdist/list', 'PrefDistMaster[pref_id]' => $pref->id]))];
                }
                echo SortableInput::widget([
                    'name' => "prefIds[0]",
                    'items' => $items,
                    'sortableOptions' => [
                        'itemOptions' => ['class' => 'btn btn-simple ui-sortable-handle'],
                        'connected' => true,
                    ],
                    'options' => ['class' => 'form-control', 'readonly' => true]
                ]); ?>
                <!--ボタン-->
                <nav class="navbar btn-menu">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                data-target="#btn_bar_box" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="btn_bar_box">
                        <div class="navbar-text">
                            <ul class="btn-box col_multi">
                                <li>
                                    <?= Html::submitButton(
                                        '<i class="glyphicon glyphicon-ok"></i>' . Yii::t('app', 'エリアの並び順、都道府県の割当と並び順を確定する'),
                                        ['class' => 'btn btn-primary btn-sm', 'data-toggle' => 'modal']); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

            </div>
        </div>
    </div>
<?php

ActiveForm::end();
?>
<?php
Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);
Pjax::end();
?>