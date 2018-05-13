<?php

namespace tests\codeception\_pages\manage;

use app\models\manage\ManageMenuMain;
use yii\codeception\BasePage;

/**
 * Represents contact page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class BaseGridPage extends BasePage
{
    public $attributes;

    /**
     * 一覧画面から新規作成画面へ遷移してチェックする
     * @param string $buttonText
     * @param ManageMenuMain $menu
     */
    public function goCreate($buttonText, $menu)
    {
        $this->actor->amGoingTo("一覧から{$menu->title}へ遷移");
        $this->actor->wait(1);
        $this->actor->click($buttonText);
        $this->actor->wait(1);
        $this->actor->seeInTitle($menu->title);
        $this->actor->see($menu->title, 'h1');
    }

    /**
     * Gridのn行目のレコードのidを取得する
     * @param int $row Gridの行
     * @return string
     */
    public function grabTableRowId($row)
    {
        $dataKey = $this->actor->grabAttributeFrom("tbody tr:nth-child($row)", 'data-key');
        preg_match('/\d+/', $dataKey, $match); // jobは複合キーだが最初にidが来る
        return $match[0];
    }

    /**
     * 操作カラムのボタンをクリックする
     * @param int $row Gridの行
     * @param int $buttonPlace 何番目のボタンなのか
     */
    public function clickActionColumn($row, $buttonPlace)
    {
        $this->actor->click("tbody tr:nth-child($row) td:last-child .btn.btn-sm.btn-inverse:nth-child($buttonPlace)");
    }

    /**
     * 指定IDの操作カラムボタンをクリックする ※ページャー対応版
     * @param int $id 指定されたレコードID
     * @param int $buttonPlace 何番目のボタンなのか
     * @param int $pageSizeMax 1ページの最大行数
     * @param boolean $pageMove ページャー移動するかどうか
     * @return boolean 念のための返り値
     */
    public function clickActionColumnById($id, $buttonPlace, $pageSizeMax, $pageMove = true)
    {
        // ページ数を取得
        if ($pageMove) {
            $pageMax = $this->actor->grabAttributeFrom('(//div[@id="grid_id"]/ul[1]/li[position()=last()-1]/a)', 'data-page');
            $pageMax = $pageMax === false ? 0 : $pageMax;
        } else {
            // ページ移動しない時は現ページのみ。
            $pageMax = 0;
        }

        // 指定IDの行を検索して操作カラムボタンをクリック
        $match = false;
        for ($i = 0; $i <= $pageMax; $i++) {
            // ページ移動
            if ($pageMove) {
                $this->actor->click("a[data-page='$i']");
                $this->actor->wait(2);
            }
            for ($j = 1; $j <= $pageSizeMax; $j++) {
                $dataKey = $this->grabTableRowId($j);
                if ($dataKey == $id) {
                    $this->clickActionColumn($j, $buttonPlace);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Gridのチェックボックスをクリックする
     * 0を入れると全選択のチェックボックスをクリックする
     * @param int $row Gridの行
     */
    public function clickCheckbox($row)
    {
        if ($row === 0) {
            $this->actor->click('//thead/tr/th[1]/div/label/span');
        } else {
            $this->actor->click("//tbody/tr[$row]/td[1]/label/span");
        }
    }

    /**
     * gridの指定の座標のtextを検査する
     * @param $row
     * @param $column
     * @param $text
     */
    public function seeInGrid($row, $column, $text)
    {
        $this->actor->see($text, "//tbody/tr[$row]/td[$column]");
    }

    /**
     * 指定した列のthをクリックする（sort用）
     * @param $column
     */
    public function clickTh($column)
    {
        $this->actor->click("//thead/tr[1]/th[$column]");
    }
}
