<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/02/04
 * Time: 16:46
 */

namespace app\common;


use yii\helpers\Json;
use yii\widgets\Pjax;
use yii\widgets\PjaxAsset;

class PostablePjax extends Pjax
{
    public $trigger = [];

    public $url;

    public $postAttribute;

    public $postAttributeName;

    public $post = [];

    /**
     * pjaxのclient scriptをrenderする
     */
    public function registerClientScript()
    {
        $id = $this->options['id'];
        $this->clientOptions['push'] = $this->enablePushState;
        $this->clientOptions['replace'] = $this->enableReplaceState;
        $this->clientOptions['timeout'] = $this->timeout;
        $this->clientOptions['scrollTo'] = $this->scrollTo;
        $this->clientOptions['url'] = $this->url;
        $this->clientOptions['type'] = 'POST';
        $this->clientOptions['container'] = '#' . $id;
        $this->clientOptions['area'] = '#' . $id;

        if (!is_string($this->postAttribute) || $this->postAttribute === '') {
            $this->clientOptions['data'] = $this->post;
            $options = Json::htmlEncode($this->clientOptions);
        } elseif ($this->postAttribute == 'value') {
            $this->clientOptions['data'] = array_merge([$this->postAttributeName => '$(this).val()'], $this->post);
            $options = Json::htmlEncode($this->clientOptions);
            $options = str_replace('"$(this).val()"', '$(this).val()', $options);
        } else {
            $this->clientOptions['data'] = array_merge([$this->postAttributeName => '$(this).attr(' . $this->postAttribute . ')'], $this->post);
            $options = Json::htmlEncode($this->clientOptions);
            $options = str_replace('"$(this).attr(' . $this->postAttribute . ')"', '$(this).attr("' . $this->postAttribute . '")', $options);
        }

        $view = $this->getView();
        if (key_exists('selector', $this->trigger) && key_exists('event', $this->trigger)) {
            $this->registerPlugin($this->trigger['selector'], $this->trigger['event'], $options);
        } else {
            foreach ($this->trigger as $trigger) {
                $this->registerPlugin($trigger['selector'], $trigger['event'], $options);
            }
        }

        PjaxAsset::register($view);
    }

    /**
     * pjaxのclient scriptをrenderする
     * @param $selector
     * @param $event
     * @param $options
     */
    private function registerPlugin($selector, $event, $options)
    {
        $js = "jQuery({$selector}).on('{$event}', function (event) {jQuery.pjax({$options});});";
        $view = $this->getView();
        $view->registerJs($js);
    }
}