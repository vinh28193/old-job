<?php

/* @var $this \yii\web\View */
/* @var $content string */

use proseeds\assets\AdminAsset;
use yii\helpers\Html;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body id="signin">
<?php $this->beginBody() ?>
<div id="wrapper">
    <div class="container form-signin">
        <?=$content?>
    </div>
</div>
<!-- /wrapper -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
