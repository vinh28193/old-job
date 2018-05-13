<?php

namespace tests\codeception\_pages;

use app\models\manage\SearchkeyMaster;
use yii\codeception\BasePage;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class JobDetailPage extends BasePage
{
    /**
     * @inheritdoc
     */
    public $route = 'kyujin/index';

    /**
     * @var array $items
     */
    public $items = [];

    /**
     * @param $items mixed
     */
    public function checkSearchKeyIcons($items)
    {
        foreach ($items as $item) {
            $this->items[$item] = $item;
            $this->actor->see($item);
        }
    }
    public function reviewSearchKeyIcon(){
        foreach ($this->items as $item){
            $this->actor->cantSee($item);
            unset($this->items[$item]);
        }

    }
}

