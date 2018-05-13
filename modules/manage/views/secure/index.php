<?php

use app\models\manage\AccessCount;
use app\models\manage\ApplicationMaster;
use proseeds\widgets\PopoverWidget;
use yii\helpers\Url;
use app\assets\GoogleChartsAsset;

/* @var $this yii\web\View */
/* @var $applicationMaster ApplicationMaster */
/* @var $accessCount AccessCount */

$this->title = Yii::t('app', 'ホーム');
$identity = Yii::$app->user->identity;
GoogleChartsAsset::register($this);
$googleChartsJs = <<<JS
google.charts.load('visualization', {packages:['corechart']});
google.charts.setOnLoadCallback(drawChart);
    // グラフの描画
    function drawChart() {
        // データ定義
        var data = new google.visualization.arrayToDataTable({$accessCount->userAgentData});
        // グラフの作成
        var chart_option = {hAxis:{format:'#,###PV'}};
        var chart = new google.visualization.BarChart(
                     document.getElementById('piechart'));
        chart.draw(data, chart_option);
    }

    // onReSizeイベント    
    window.onresize = function(){
        drawChart();
    }
JS;
$this->registerJs($googleChartsJs);
?>
<script type="text/javascript">
</script>
<style>
    div.box{
        float: left;
        height:10px;
        width:20px;
        margin-top:4px;
        margin-right:2px;
    }
    div.pv_pc{
        background-color: #4169e1;
    }
    div.pv_sp{
        background-color: #3cb371;
    }
</style>
<h1 class="heading"><span class="glyphicon glyphicon-dashboard"></span><?= $this->title ?></h1>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- box -->
            <div class="row">
                <div class="col-xs-12 col-sm-6 col-lg-4">
                    <a href="<?= Url::to('secure/application/list') ?>" class="dashboad_box dashboad_box02">
                        <div class="dashboad_icon bg_green">
                            <span class="glyphicon glyphicon-envelope"></span>
                        </div>
                        <div class="dashboad_text">
                            <p class="dashboad_title dashboad_title_oubo"><?= Yii::t('app', '応募者'); ?><br/><span class="small"><?= Yii::t('app', '(削除原稿も含む)'); ?></span></p>
                        </div>
                        <div class="dashboad_fig">
                            <p class=""><?= $applicationMaster->totalCount ?></p>
                        </div>
                    </a>
                </div>
            </div>
            <!-- /box -->


            <div class="main_box">
                <!-- half -->
                <div class="col-md-6">
                    <h2><?= Yii::t('app', 'アクセス数'); ?><?= PopoverWidget::widget([
                            'dataHtml' => true,
                            'dataContent' => Yii::t('app', '本日と昨日の応募数、求人情報閲覧数が確認できます。<br>※画面反映まで3~5分程、タイムラグがありますのでご了承ください。'),
                        ]) ?></h2>
                    <table class="table table-bordered" style="margin-top:60px;">
                        <thead>
                        <tr>
                            <th rowspan="2"></th>
                            <th colspan="2"><?= Yii::t('app', '応募数'); ?></th>
                            <th colspan="2"><?= Yii::t('app', '求人情報閲覧数'); ?></th>
                        </tr>
                        <tr>
                            <th><?= Yii::t('app', 'PC'); ?></th>
                            <th><?= Yii::t('app', 'スマホ'); ?></th>
                            <th><?= Yii::t('app', 'PC'); ?></th>
                            <th><?= Yii::t('app', 'スマホ'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= Yii::t('app', '本日'); ?></td>
                            <td><?= $applicationMaster->todayPcCount ?></td>
                            <td><?= $applicationMaster->todaySmartPhoneCount ?></td>
                            <td><?= $accessCount->todayPcCount ?></td>
                            <td><?= $accessCount->todaySpCount ?></td>
                        </tr>
                        <tr>
                            <td><?= Yii::t('app', '昨日'); ?></td>
                            <td><?= $applicationMaster->yesterdayPcCount ?></td>
                            <td><?= $applicationMaster->yesterdaySmartPhoneCount ?></td>
                            <td><?= $accessCount->yesterdayPcCount ?></td>
                            <td><?= $accessCount->yesterdaySpCount ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /half -->


                <!-- half -->
                <div class="col-md-6">
                    <h2><?= Yii::t('app', 'ブラウザ'); ?><?= PopoverWidget::widget([
                            'dataHtml' => true,
                            'dataContent' => Yii::t('app', '求人詳細画面にアクセスしたブラウザが確認できます。<br>
                                                            <div class="box pv_pc"></div>PV数(PC)<br>
                                                            <div class="box pv_sp"></div>PV数(スマートフォン)<br>
                                                            ※画面反映まで3~5分程、タイムラグがありますのでご了承ください。'),
                        ]) ?></h2>
                    <?php if (!empty($accessCount->userAgentData)) : ?>
                        <div id="piechart"style="width:100%; height:200pt;"></div>
                    <?php else : ?>
                        <?= Yii::t('app', 'アクセスはありません。'); ?>
                    <?php endif; ?>
                </div>
                <!-- /half -->
            </div>


        </div>
    </div>
</div>
<!-- /container -->