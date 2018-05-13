<?php

namespace app\assets;

use yii\web\AssetBundle;

class EventSourceAsset extends AssetBundle
{
    public $sourcePath = '@bower/event-source-polyfill';
    public $js = ['eventsource.js'];
}
