<?php

namespace app\common;

use yii\base\InvalidConfigException;
use yii\bootstrap\Html;
use yii\bootstrap\Widget;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

class WindowOpen extends Widget
{
    public $tag = 'button';
    public $tagLabel = '<i class="glyphicon glyphicon-eye-open"></i>';
    public $tagOptions = [];
    public $dataToggle = 'preview';

    public $name = "_blank";
    public $url = '#';
    public $options = [];
    public function init(){
        parent::init();
        $this->registerWindownOptions();
        $this->registerTagOptions();
        if(!$this->url || $this->url === '#') throw new InvalidConfigException('The "url" property must be set.');

$js = <<<JS
jQuery('$this->tag[data-toggle ="$this->dataToggle"]').on("click", function(e) {
    window.open('$this->url', '$this->name', '$this->options');
});
JS;
    $this->view->registerJs($js);
    }
    public function run()
    {
        return $this->tag === 'a' ? Html::a($this->tagLabel,false,$this->tagOptions) : Html::tag($this->tag,$this->tagLabel,$this->tagOptions);
    }
    public function registerWindownOptions()
    {
        $windowOptionDefault = [
            'id' => 'windown',
            'width' => 1024,
            'height' => 800
        ];
        
        if(is_array($this->options)){
            $options = ArrayHelper::merge($windowOptionDefault,$this->options);
            $newOptions = '';
            foreach ($options as $key => $option) {
                $newOptions .= $key .'=' . $option . ',';
            }
            $this->options = substr($newOptions,0,-1);
            //$this->options = Json::encode($this->options, JSON_UNESCAPED_SLASHES);
        }else{
            throw new InvalidConfigException("WindowOpen::$options must an array instance", 1);
        }
    }
    public function registerTagOptions()
    {
        $tagOptionDefault = [
            'data-toggle' => $this->dataToggle,
        ];
        $this->tagOptions = ArrayHelper::merge($tagOptionDefault,$this->tagOptions);
    }
}