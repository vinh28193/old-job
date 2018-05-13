<?php

namespace app\common\widget;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\LinkSorter;

/**
 * ドロップダウン形式のsorterを出力する
 * 並び順は降順固定
 */
class DropDownSorter extends LinkSorter
{
    /**
     * idをセット
     */
    public function init()
    {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
    }

    /**
     * widget実行
     */
    public function run()
    {
        parent::run();
        $this->registerClientScript();
    }

    /**
     * 並び替えDropDownを生成する
     * 並び順はそのattributeの降順になる
     * @return string
     */
    protected function renderSortLinks()
    {
        // 親そのまま
        $attributes = empty($this->attributes) ? array_keys($this->sort->attributes) : $this->attributes;
        // 現在のsort状況取得
        $directions = $this->sort->getAttributeOrders();
        $selection = $this->sort->defaultOrder ? key(array_slice($this->sort->defaultOrder, 0, 1)) : null;
        $links = [];
        foreach ($attributes as $attribute) {
            // todo 文字置換せずに書けないか調査・検討
            $url = str_replace($attribute, '-' . $attribute, $this->sort->createUrl($attribute));
            $links[$url] = $this->sort->attributes[$attribute]['label'];
            if (ArrayHelper::getValue($directions, $attribute) === SORT_DESC) {
                $selection = $url;
            }
        }

        return Html::dropDownList('resultOrder', $selection, $links, $this->options);
    }

    /**
     * 必要なJavaScriptをRegisterする
     */
    public function registerClientScript()
    {
        $js = <<<JS
$("#{$this->options['id']}").on('change', function() {
  location.href = this.value;
})
JS;
        $this->view->registerJs($js);
    }
}
