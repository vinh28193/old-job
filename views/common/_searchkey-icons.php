<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/03/06
 * Time: 15:34
 */
use app\controllers\KyujinController;
use yii\bootstrap\Html;

/* @var $searchKeyIconContents array */
?>
<p class="mod-iconSearchKey">
    <?php foreach ($searchKeyIconContents as $key => $content) {
        if ($key == KyujinController::INITIAL_DISPLAY_ICONS) {
            echo Html::beginTag('span', ['id' => 'showIcons']);
            echo Html::a(Yii::t('app', '...全て表示'), 'javascript:void(0);', [
                'style' => 'font-size: 80%;vertical-align:bottom',
                'onclick' => 'javascript:$("#moreIcons").show();$("#showIcons").hide();'
            ]);
            echo Html::endTag('span');
            echo Html::beginTag('span', ['id' => 'moreIcons', 'style' => 'display:none']);
        }
        echo Html::tag('span', $content, ['class' => 'icon icon-merit']);
    }
    if (count($searchKeyIconContents) > KyujinController::INITIAL_DISPLAY_ICONS) {
        Html::endTag('span');
    }
    ?>
</p>
