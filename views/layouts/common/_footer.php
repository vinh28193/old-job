<?php
use yii\helpers\Url;
?>

<div class="footer">
    <footer class="container">
        <ul class="footer-nav">
            <li><a href="http://www.pro-seeds.co.jp/" target="_blank"><?= Yii::t('app', "会社案内") ?></a></li>
            <li><a href="<?= Url::to(['/policy/index', 'policy_no' => 5], true) ?>"><?= Yii::t('app', "個人情報保護方針") ?></a></li>
            <li><a href="<?= Url::to(['/policy/index', 'policy_no' => 7], true) ?>"><?= Yii::t('app', "利用規約") ?></a></li>
        </ul>
    </footer>
</div>
<div class="copyright"><p>Copyright&copy;pro*seeds. All rights reserved.</p></div>