<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

/* @var $model app\models\JobMasterDisp */
/* @var $hotJob app\models\manage\HotJob.php */

?>

<div class="mod-jobResultBox">
    <div class="mod-jobResultBox__body">
        <?php
        $url = Url::toRoute(['/kyujin/' . $model->job_no]);
        $imgPath = ($model->getJobImagePath(1)) ?
//            Html::encode($model->getJobImagePath(1)) : '';
            Html::encode($model->getJobImagePath(1)) : $model::NO_IMAGE_PATH;

        echo Html::beginTag('a', ['href' => $url]);
        echo '<span class="img">' . Html::img($imgPath, ['alt' => $model->clientModel->client_name]) . '</span>';

        for ($i = 1; $i <= 4; $i++) {
            $textKey = $hotJob->{"text" . $i};

            if (!empty($textKey)) {
                if ($textKey == 'disp_start_date' || $textKey == 'disp_end_date') {
                    // unixtimeを日付フォーマットに変更
                    $text = Yii::$app->formatter->asDate($model->$textKey);
                } elseif ($textKey == 'client_master_id') {
                    //掲載企業名を取得
                    $text = $model->clientModel->client_name;
                } elseif ($textKey == 'corpLabel') {
                    //代理店名を取得
                    $text = $model->clientMaster->corpModel->corp_name;
                } else {
                    //テキスト内容を取得
                    $text = $model->$textKey;
                }

                // 設定以上の文字数を「...」に変換
                $textLength = $hotJob->{"text" . $i . "_length"}; //設定している文字数上限
                $text = StringHelper::truncate($text, $textLength, '...');

                //text1-4に応じてcssを振り分け
                if ($i == 1) {
                    echo '<h3 class="title">' . Html::encode($text) . '</h3>';
                } else {
                    echo '<p class="description">' . Html::encode($text) . '</p>';
                }
            }
        }
        echo Html::endTag('a');
        ?>
    </div>
</div>
