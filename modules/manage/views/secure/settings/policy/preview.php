<?php
use app\assets\MainAsset;
use yii\helpers\Html;

MainAsset::register($this);
/* @var $this yii\web\View */
/* @var $model \app\models\manage\Policy */

$this->title = $model->policy_name;
?>

<?php $this->beginPage() ?>
    <!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <?= Html::tag('title', Html::encode($this->title)) ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            echo Html::tag('pre', $model->policy, ['class' => 'well well-sm text-left']);
            ?>
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
<?php $this->endPage() ?>