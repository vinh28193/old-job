<?php

namespace app\assets;

use app\common\Helper\Html;
use app\models\manage\JobMaster;
use app\modules\manage\models\Manager;
use Yii;
use proseeds\assets\BootBoxAsset;
use yii\helpers\Json;
use yii\web\View;

/**
 * 求人原稿登録・編集の確認モーダル用アセット
 */
class JobBootBoxAsset extends BootBoxAsset
{
    /**
     * @param View $view
     * @param JobMaster $model
     * @param string $formName
     * @param string $beforeSubmit
     */
    public static function jobConfirmBeforeSubmit($view, $model, $formName = 'form', $beforeSubmit = '')
    {
        if (!Yii::$app->tenant->tenant->review_use) {
            // 審査機能OFF時はデフォルトメッセージのみを表示
            static::confirmBeforeSubmit($view, static::defaultMessage($model), $formName, $beforeSubmit);
            return;
        }

        $withReviewMessages = static::commonMessages($model);
        $saveOnlyMessages = $withReviewMessages;
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        if (!$identity->isOwner()) {
            // 運営元管理者以外は審査依頼、一時保存時のメッセージを付与
            $withReviewMessages[] = static::withReviewMessage();
            $saveOnlyMessages[] = static::saveOnlyMessage();
        }

        $withReview = Json::encode(implode('<br>', $withReviewMessages));
        $saveOnly = Json::encode(implode('<br>', $saveOnlyMessages));

        $js = <<<JS
$('{$formName}').on('beforeSubmit', function(e){
    var form = $(this);
    var submitType = $('#submitType').val();
    var message = {$withReview};

    if (submitType == 'saveOnly') {
        message = {$saveOnly};
    }

    bootbox.confirm(message, function(result){
        if (result) {
            form.off('beforeSubmit');
            {$beforeSubmit}
            form.yiiActiveForm('submitForm');
        } else {
            $('#submitType').val('default');
        }
    });

    return false;
});
JS;
        $view->registerJs($js);
    }

    /**
     * 共通メッセージ
     * @param JobMaster $model
     * @return array
     */
    private static function commonMessages($model)
    {
        $messages = [];
        if ($model->isNotReviewer()) {
            // ステータス違いの場合注意メッセージを追加
            $messages[] = Html::tag('span', Yii::t('app', '【注意】現在{reviewStatus}です。', ['reviewStatus' => $model->jobReviewStatus->name]), ['class' => 'text-red', 'style' => ['front-weight' => 'bold']]);
        }
        $messages[] = static::defaultMessage($model);

        return $messages;
    }

    /**
     * 基本メッセージ
     * @param $model
     * @return string
     */
    private static function defaultMessage($model)
    {
        return $model->isNewRecord ? Yii::t('app', '求人原稿情報を登録してもよろしいですか？') : Yii::t('app', '求人原稿情報を変更してもよろしいですか？');
    }

    /**
     * 一時保存メッセージ
     * @return string
     */
    private static function saveOnlyMessage()
    {
        return Yii::t('app', 'また<span class="text-red">審査依頼されず</span>に保存されますがよろしいですか？');
    }

    /**
     * 審査依頼込みメッセージ
     * @return string
     */
    private static function withReviewMessage()
    {
        $message = Yii::t('app', 'また続けて<span class="text-red">審査依頼画面へ遷移</span>します。');
        $message .= '<div>';
        $message .= '<img class="center-block w80l w70m" src="/pict/flow.png">';
        $message .= '</div>';
        $message .= '<p class="text-red">';
        $message .= '<span style="font-weight: bold;">';
        $message .= Yii::t('app', '掲載中の原稿を編集し、「登録」すると再度審査が必要となります。');
        $message .= '</span><br>';
        $message .= Yii::t('app', '(審査が完了するまで掲載は非表示となります)') . '<br>';
        $message .= Yii::t('app', '掲載をご希望の場合は、次画面の「審査依頼」ボタンをクリックし、審査依頼を行ってください。');
        $message .= '</p>';

        return $message;
    }
}
