<?php
/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/10/20
 * Time: 19:58
 */

namespace app\common;

use yii\base\Component;

class ManageMenuConfigs extends Component
{
    public $menus;
    private $items;

    public function __construct($config = [])
    {
        parent::__construct($config);
        foreach($this->menus as $name => $conf){
            $this->items[$name] = \Yii::createObject(['class' =>ColumnSet::className()] + $conf);
        }
    }

    public function __get($name)
    {
        return $this->items[$name];
    }
}