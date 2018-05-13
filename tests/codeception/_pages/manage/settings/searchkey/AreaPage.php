<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/01/22
 * Time: 17:31
 */

namespace tests\codeception\_pages\manage\settings\searchkey;


use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Pref;

class AreaPage extends BaseSearchkeyPage
{
    /**
     * 都道府県を指定した都道府県の後ろ、もしくは指定したエリアの後ろに移動する
     * @param Pref $pref
     * @param Area|Pref|null $to
     */
    public function movePref($pref, $to)
    {
        if ($to instanceof Area) {
            $this->actor->amGoingTo("{$pref->pref_name}を{$to->area_name}へ移動");
            $target = "//li[@data-key='area{$to->id}']/div/div[2]";

        } elseif ($to instanceof Pref) {
            $this->actor->amGoingTo("{$pref->pref_name}を{$to->pref_name}の後ろへ移動");
            $target = "//*[text()='{$to->pref_name}']";
        } else {
            $this->actor->amGoingTo("{$pref->pref_name}を欄外へ移動");
            $target = '//div[@id="fixedBox"]/ul';
        }
        $this->actor->dragAndDrop("//*[text()='{$pref->pref_name}']", $target);
    }

    /**
     * エリアを指定したエリアの後ろに移動する
     * @param Area $fromArea
     * @param int $to
     */
    public function moveArea($fromArea, $to)
    {
        $this->actor->amGoingTo("{$fromArea->area_name}を{$to}番目へ移動");
        $this->actor->dragAndDrop("//*[text()='{$fromArea->area_name}']", "//form/ul/li[{$to}]/div/div[2]/ul");
    }

    /**
     * リロードして完了メッセージが出ていないことをチェックする
     */
    public function reload()
    {
        $this->actor->amGoingTo('リロードする');
        $this->actor->reloadPage();
        $this->actor->wait(3);
        $this->actor->cantSee('完了しました。');
    }
}