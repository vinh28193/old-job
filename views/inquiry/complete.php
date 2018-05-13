<?php
/**
 * @var $this \yii\web\View
 */
$this->title                   = Yii::t('app', 'お問い合わせ完了');
$this->params['breadcrumbs'][] = $this->title;
$currentClass['complete']      = true;
$this->params['bodyId']        = 'inquiry-complete';

// no index metaタグを追加
$this->registerMetaTag([
    'name' => 'robots',
    'content' => 'noindex',
]);
?>
<div class="container subcontainer">
    <div class="row">
        <!--▼ここからコンテンツスタート▼-->
        <div class="col-sm-12">

            <?= $this->render('_flow', ['currentClass' => $currentClass]) ?>

            <div class="mod-subbox-wrap">
                <h1 class="mod-h1"><?= Yii::t('app', 'お問い合わせが完了しました。') ?></h1>
                <div class="mod-subbox">
                    <h1 class="mod-subbox__thanks"><?= Yii::t('app', 'お問い合わせありがとうございました。') ?></h1>
                    <p class="txt"> <?= Yii::t('app', 'この度は本サービスへのお問い合わせをいただきありがとうございます。') ?><br>
                        <?= Yii::t('app', '担当者より改めてご連絡させていただきますのでしばらくお待ち下さい。 尚、弊社が休業日の場合はご連絡が遅れる場合がございますのでご了承下さい。') ?>
                    </p>
                    <p>
                        <?= Yii::t('app', '担当者からの連絡がない場合は、記入情報の入力間違いの可能性がございます。') ?><br>
                        <?= Yii::t('app', 'お手数ですが、再度お問い合わせいただくかお電話にてお問い合わせください。') ?>
                    </p>

                    <div class="mod-box-center w60 w90-sp">
                        <p class="mod-btn2"><a href="/"><?= Yii::t('app', 'トップページへ戻る') ?></a></p>
                    </div>
                </div>
            </div>

        </div><!-- / .col-sm-12 -->
    </div>
</div>
