<?php

use app\modules\manage\models\Manager;
use app\modules\manage\models\MenuCategory;
use app\models\manage\SiteMaster;
use yii\helpers\Html;
use yii\helpers\Url;


/** @var Manager $identity */
$identity = Yii::$app->user->identity;
?>
<header role="banner">
    <div class="drawer-header">
        <button type="button" class="drawer-toggle drawer-hamburger">
            <span class="sr-only">toggle navigation</span>
            <span class="drawer-hamburger-icon"></span>
        </button>
    </div>
    <div class="drawer-main drawer-default">
        <nav class=" drawer-nav" role="navigation">
            <div class="drawer-brand">
                <a href="../">JobMaker</a>
            </div>

            <ul class="drawer-menu">
                <li class="drawer-menu-item drawer-dropdown"><a href="/"><span
                                class="glyphicon glyphicon-home"></span><?= SiteMaster::find()->one()->site_name ?></a>
                </li>
                <li class="drawer-menu-item drawer-dropdown<?php echo Yii::$app->requestedRoute == Url::to('manage/secure/index') ? Html::encode(' active') : '' ?>">
                    <a href="<?php echo Url::to('/manage/secure'); ?>"><span
                                class="glyphicon glyphicon-dashboard"></span><?php echo Yii::t('app', 'ホーム') ?></a>
                </li>
                <?php foreach ($identity->myMenu as $menu): ?>
                    <?php if (in_array($menu->manage_menu_category_no, MenuCategory::DEFAULT_SETTING_MENU_NUMBERS)) continue; ?>
                    <?php
                    // TODO:代理店のみギャラリー機能を使えなくしている。仕様が決まり次第、修正する。
                    if (!($menu->icon_key == 'picture' && $identity->myRole == Manager::CORP_ADMIN)):
                        ?>
                        <li id="<?php echo 'menu-cate-' . Html::encode($menu->id); ?>"
                            class="drawer-menu-item dropdown drawer-dropdown<?php if ($menu->id == MenuCategory::SEARCH_KEY_CATEGORY_NO) {
                                echo $menu->isActive() ? Html::encode(' active') . ' col2' : ' col2';
                            }
                            echo $menu->isActive() ? Html::encode(' active') : '' ?>">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
                                <span
                                        class="glyphicon glyphicon-<?php echo Html::encode($menu->icon_key ? $menu->icon_key : 'th'); ?>"></span>
                                <?php echo Html::encode(Yii::t('app', $menu->name)); ?>
                            </a>

                            <ul class="drawer-submenu dropdown-menu" role="menu">
                                <?php foreach ($menu->items as $item): ?>
                                    <li>
                                        <a id="<?php echo Html::encode(sprintf('navi-item%02d', $item->id)); ?>"
                                           href="<?php echo Html::encode($item->href); ?>">
                                            <span
                                                    class="glyphicon glyphicon-<?php echo Html::encode($item->icon_key ? $item->icon_key : 'th'); ?>"></span>
                                            <?php echo Html::encode(Yii::t('app', $item->name)); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif ?>
                <?php endforeach; ?>
                <?php if ($identity->myRole === Manager::OWNER_ADMIN) {
                    echo '<li id="menu-cate-8" class="drawer-menu-item"><a href=' . Url::toRoute('secure/settings/list') . '><span class="glyphicon glyphicon-search"></span>' . Yii::t('app', 'サイト設定') . '</a></li>';
                } ?>
                <li class="drawer-menu-item drawer-dropdown no-mobile block">
                    <a href="#" id="menu"><span
                                class="glyphicon glyphicon-chevron-left"></span><?php echo Yii::t('app', 'メニューを閉じる') ?>
                    </a>
                </li>

            </ul>

            <div class="drawer-footer"><span></span></div>
        </nav>
    </div>
</header>
