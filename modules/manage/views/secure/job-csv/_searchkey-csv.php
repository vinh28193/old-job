<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use app\models\manage\SearchkeyMaster;
use app\modules\manage\controllers\secure\CsvHelperController;

/* @var $this yii\web\View */
/* @var SearchkeyMaster[] $searchkeys 有効な検索キーの配列 */
$searchkeys = Yii::$app->searchKey->searchkeys;
$clientChargePlanLabel = Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label;

?>
<div>
    <p class="mgb10 mgl5">
        <a id="hide_btn2" data-toggle="collapse" href="#hide_box" aria-expanded="false"
           aria-controls="hide_box" class= 'btn btn-simple btn-sm'><?= Yii::t("app", Html::icon("plus-sign") . Yii::t("app", "検索キーコードを確認する")) ?></a>
    </p>

    <div id="hide_box" class="collapse" aria-expanded="false" style="height: 0px;">
        <div>
            <?php
            $content = Html::beginTag("div");

            $content .= Html::beginTag("ul", ['style' => Html::cssStyleFromArray(['margin-bottom' => '5px'])]);
            $content .= Html::tag("li", Yii::t("app", "画面で確認したい方はこちら"));
            $content .= Html::endTag("ul");

            $button = Html::a(Html::icon("list") . Yii::t("app", "検索キーコード一覧"), "#", ['class' => 'btn btn-simple btn-sm mgb10 mgl5', 'onclick' => "javascript:window.open('"
                            . Url::to(['secure/csv-helper/job', 'helperType' => CsvHelperController::PLAN]) . "', 'searchkey')"]);
            $content .= Html::tag("span", $button);
            $content .= Html::endTag("div");
            echo $content;

            $content = Html::beginTag("div");

            $content .= Html::beginTag("ul", ['style' => Html::cssStyleFromArray(['margin-bottom' => '5px'])]);
            $content .= Html::tag("li", Yii::t("app", "CSVをダウンロードして確認したい方はこちら"));
            $content .= Html::endTag("ul");

            $buttons = [];
            $buttons[] = Html::a(Html::icon("download-alt") . Yii::t("app", $clientChargePlanLabel),
                Url::to("client-charge-plan-csv-download"), ['class' => 'btn btn-simple btn-sm mgb5 mgl5']);

            foreach ($searchkeys as $searchkey) {
                $buttons[] = Html::a(Html::icon("download-alt") . Yii::t("app", $searchkey->searchkey_name),
                    Url::to(["searchkey-csv-download", 'id' => $searchkey->id]), ['class' => 'btn btn-simple btn-sm mgb5 mgl5']);
            }

            foreach ($buttons as $button) {
                $content .= Html::tag("span", $button);
            }

            $content .= Html::endTag("div");
            echo $content;
            ?>
        </div>
    </div>

</div>
