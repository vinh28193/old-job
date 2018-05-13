<?php

use app\models\manage\SearchkeyMaster;
use app\models\manage\ManageMenuCategory;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $settingMenus ManageMenuCategory[] */

$this->title = Yii::t('app', 'サイト設定');
$this->params['breadcrumbs'][] = $this->title;

$css = <<<CSS
.valid-label {
  color: #2ecc71;
}
CSS;
$this->registerCss($css);

?>

<h1 class="heading"><?= Html::icon('search') . Html::encode($this->title) ?></h1>
<p class="alert alert-warning"><?= Yii::t('app', 'サイト設定を行います。設定したい項目を選択してください。') ?></p>
<div class="container" id="view-mode">

    <?php
    $dataId = 0;
    foreach ($settingMenus as $menu) :
        ?>
        <h3 class="bg-info pd5"><?= Html::encode($menu['title']); ?></h3>
        <div class="row">
            <?php foreach ($menu->items as $item) : ?>
                <?php $dataId++; ?>
                <div class="col-sm-12 col-md-6" data-id="<?= $dataId; ?>">
                    <div class="panel panel-default clickable-panel"
                         onclick="location.href = '<?= Html::encode($item->href); ?>'">
                        <input type="hidden" name="manage-shortcut[]" value="<?= $dataId; ?>">
                        <div class="panel-body">
                            <h4 class="panel-title"><span
                                    class="glyphicon glyphicon-pencil"></span>
                                <?php
                                if ($item->searchkeyValidChk === SearchkeyMaster::FLAG_INVALID) {
                                    //検索キーが無効の場合searchkey_nameを表示
                                    echo Html::encode($item->searchkeyName);
                                } elseif ($item->searchkeyValidChk === SearchkeyMaster::FLAG_VALID) {
                                    //検索キーが有効の場合searchkey_nameに（公開中）と追記して表示
                                    echo Html::encode($item->searchkeyName) . Html::tag('span', Yii::t('app', '（公開中）'), ['class' => 'valid-label']);
                                } else {
                                    //それ以外の場合 manage_menu_mainのnameを表示
                                    echo Html::encode(Yii::t('app', $item->name));
                                }
                                ?>
                            </h4>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endforeach ?>
</div>