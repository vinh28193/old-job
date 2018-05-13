<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/03/12
 * Time: 15:49
 */
// KeepWidgetとkeepAssetに強く依存します
use app\common\Keep;
use yii\helpers\Url;

/** @var \yii\web\View $this */

$count = Keep::count();
$url = Url::to('/keep/');

$css = <<<CSS
.pc_keepListBtn a {
    display: table;
    color: #fff;
    background: #019fe6;
    padding: 10px 6px 10px;
    font-size: 15px;
    text-align: center;
    border-radius: 4px 0 0 4px;
    width: 85px;
    transition: .1s ease-out;
    -o-transition: .1s ease-out;
    -moz-transition: .1s ease-out;
    -webkit-transition: .1s ease-out;
    -ms-transition: .1s ease-out;
}
.pc_keepListBtn a:hover {
    width: 85px;
    background: #06aaf4;
    color: #ffd200;
}
.pc_keepListBtn a span.keepListTtl{
    font-size: 10px;
    line-height: 12px;
    padding: 0;
    display: table-cell;
    vertical-align: middle;
    width: 50px;
}
.pc_keepListBtn a span {
    display: block;
    font-size: 16px;
}
.pc_keepListBtn a span.keepCountShow{
    display: table-cell;
    width: 30px;
    text-align: center;
    border-radius: 30px;
    height: 25px;
    background: #fff;
    color: #fc8300;
    font-size: 12px;
    vertical-align: middle;
}

.sp_keepListBtn a span.keepListTtl{
    font-size: 10px;
    line-height: 12px;
    padding: 0;
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    width: 35px;
}
.sp_keepListBtn a span.fa {
    color: #ffd200;
    display: block;
    text-align: center;
    font-size: 16px;
}
.sp_keepListBtn a span.keepCountShow{
    display: table-cell;
    width: 30px;
    text-align: center;
    border-radius: 30px;
    height: 25px;
    background: #fff;
    color: #fc8300;
    font-size: 12px;
    vertical-align: middle;
}
CSS;
$this->registerCss($css);

?>

<p class="pc_keepListBtn hide-sp">
    <a href="<?= $url ?>">
        <span class="keepListTtl">
            <span class="fa fa-star"></span>
            <?= Yii::t('app', 'キープ') ?>
        </span>
        <span class="keepCountShow"><?= $count ?></span>
    </a>
</p>

<p class="sp_keepListBtn hide view-sp">
    <a href="<?= $url ?>">
    <span class="keepListTtl">
      <span class="fa fa-star"></span>
        <?= Yii::t('app', 'キープ') ?>
    </span>
        <span class="keepCountShow"><?= $count ?></span>
    </a>
</p>
