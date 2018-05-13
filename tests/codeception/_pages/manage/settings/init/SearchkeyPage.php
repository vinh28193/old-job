<?php

namespace tests\codeception\_pages\manage\settings\init;

use app\common\ProseedsFormatter;
use app\models\manage\SearchkeyMaster;
use tests\codeception\_pages\manage\BaseGridPage;
use Yii;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class SearchkeyPage extends BaseGridPage
{

    public $route = '/manage/secure/settings/list';

    /**
     * @var $menu \app\models\manage\ManageMenuMain
     */
    public $menu;

    /**
     * 遷移できることを確認する
     * @param $menu \app\models\manage\ManageMenuMain
     */
    public function go($menu)
    {
        $this->actor->amGoingTo("{$menu->title}へ遷移");
        $this->menu = $menu;
        $this->actor->wait(1);
        $this->actor->click("//h4[contains(text(),'$menu->title')]");
        $this->actor->wait(3);
        $this->actor->seeInTitle($menu->title);
        $this->actor->see($menu->title, 'h1');
    }

    /**
     * モーダルを作る
     * @param $row integer
     */
    public function openModal($row)
    {
        $this->clickActionColumn($row, 1);
        $this->actor->wait(2);
        $this->actor->see('検索キー設定変更', 'div.modal-header');
    }

    /**
     * rowのモーダルを閉じる
     */
    public function closeModal()
    {
        $this->actor->click('閉じる');
        $this->actor->wait(1);
    }

    /**
     * 変更ボタンを押すとlistに戻ることを確認する
     */
    public function submitModal()
    {
        $this->actor->click('変更');
        $this->actor->wait(3);
        $this->actor->seeInTitle($this->menu->title);
        $this->actor->see($this->menu->title, 'h1');
        $this->actor->cantSeeElement('#modal');
        $this->actor->see('更新が完了しました。', 'p.alert');
    }

    /**
     * リロードして完了メッセージが出ていないことをチェックする
     */
    public function reload()
    {
        $this->actor->amGoingTo('リロードする');
        $this->actor->reloadPage();
        $this->actor->wait(3);
        $this->actor->cantSee('更新が完了しました。');
    }

    /**
     * inputに入力して入力内容をキャッシュする
     * @param $attribute
     * @param $value
     */
    public function fillInputAndRemember($attribute, $value)
    {
        $this->actor->fillField("#searchkeymaster-$attribute", $value);
        $this->attributes[$attribute] = $value;
    }

    /**
     * radioをチェックして入力内容をキャッシュする
     * @param $attribute
     * @param $value
     */
    public function fillRadioAndRemember($attribute, $value)
    {
        $this->actor->selectOption("//input[@name='SearchkeyMaster[{$attribute}]']", $value);
        $this->attributes[$attribute] = $value;
    }

    /**
     * ラジオボタンではなくテキストが表示されている
     * @param $attribute
     * @param $text
     */
    public function cantFillRadioButSeeText($attribute, $text)
    {
        $this->actor->cantSeeElement('radio', ['id' => "#searchkeymaster-$attribute"]);
        $this->actor->see($text);
    }

    /**
     * 共通して変更可能な項目(以下)に関してinputの内容をチェックする
     * searchkey_name, sort, is_on_top
     * @param null $model
     */
    public function checkCommonItems($model = null)
    {
        if ($model instanceof SearchkeyMaster) {
            $this->actor->seeInField('#searchkeymaster-searchkey_name', $model->searchkey_name);
            $this->actor->seeInField('#searchkeymaster-sort', $model->sort);
            $this->actor->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_on_top]'][@value='{$model->is_on_top}']");
        } else {
            $this->actor->seeInField('#searchkeymaster-searchkey_name', $this->attributes['searchkey_name']);
            $this->actor->seeInField('#searchkeymaster-sort', $this->attributes['sort']);
            $this->actor->seeCheckboxIsChecked("//input[@name='SearchkeyMaster[is_on_top]'][@value='{$this->attributes['is_on_top']}']");
        }
    }

    /**
     * モーダル内の各項目の変更可or不可をチェックする
     * @param $dynamicAttributes
     * @param $staticAttributes
     */
    public function checkModalItems($dynamicAttributes, $staticAttributes)
    {
        foreach ($dynamicAttributes as $attribute) {
            $this->actor->seeElementInDOM("#searchkeymaster-{$attribute}");
        }
        foreach ($staticAttributes as $attribute) {
            $this->actor->dontSeeElementInDOM("#searchkeymaster-{$attribute}");
        }
    }

    /**
     * @param SearchkeyMaster $model
     * @param int $row
     * @param string $isCategoryLabel
     * @param string $searchInputTool
     */
    public function checkGridValues($model, $row, $isCategoryLabel = null, $searchInputTool = null)
    {
        if (!$searchInputTool) {
            $list = SearchkeyMaster::getSearchInputTool();
            $searchInputTool = $list[$model->search_input_tool];
        }
        if (!$isCategoryLabel) {
            $list = SearchkeyMaster::getIsCategoryLabel();
            $isCategoryLabel = $list[$model->is_category_label];
        }

        /** @var ProseedsFormatter $formatter */
        $formatter = Yii::$app->formatter;
        $this->seeInGrid($row, 1, $model->searchkey_name);
        $this->seeInGrid($row, 2, $model->sort);
        $this->seeInGrid($row, 3, $formatter->asIsOnTop($model->is_on_top));
        $this->seeInGrid($row, 4, $formatter->asIsAndSearch($model->is_and_search));
        $this->seeInGrid($row, 5, $searchInputTool);
        $this->seeInGrid($row, 6, $isCategoryLabel);
        $this->seeInGrid($row, 7, $formatter->asIsIconFlg($model->icon_flg));
        $this->seeInGrid($row, 8, $formatter->asValidChk($model->valid_chk));
    }
}
