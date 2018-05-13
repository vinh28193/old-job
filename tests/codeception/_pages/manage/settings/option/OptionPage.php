<?php

namespace tests\codeception\_pages\manage\settings\option;

use app\models\manage\ManageMenuMain;
use app\models\manage\BaseColumnSet;
use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 * @property string $optionName
 */
class OptionPage extends BaseGridPage
{
    public $route = '/manage/secure/settings/list';
    /** @var ManageMenuMain */
    public $menu;
    /** column_explainの表示制御のdefault値 */
    const DEFAULT_FLAG = 0;

    /**
     * サイト設定画面から項目設定画面へ遷移する
     * その際にmenuも代入する
     * @param ManageMenuMain $menu
     */
    public function go($menu)
    {
        $this->actor->amGoingTo("{$menu->name}へ遷移");
        $this->menu = $menu;
        $this->actor->wait(1);
        $this->actor->click("//h4[contains(text(), '$menu->title')]"); // CSSセレクタでやると厄介そうなのでXPath使ってます
        $this->actor->wait(3);
        $this->actor->seeInTitle($menu->title);
        $this->actor->see($menu->title, 'h1');
    }

    /**
     * n行目の変更ボタンを押してモーダルを表示する
     * @param int $row
     */
    public function openModal($row)
    {
        $this->clickActionColumn($row, 1);
        $this->actor->wait(2);
        $this->actor->see('項目変更', 'div.modal-header');
    }

    /**
     * モーダルを閉じる
     */
    public function closeModal()
    {
        // 閉じるをクリック
        $this->actor->click('閉じる');
        $this->actor->wait(1);
    }

    /**
     * モーダルのsubmitが正常に行われていることを検査
     */
    public function submitModal()
    {
        // 変更をクリック
        $this->actor->click('変更');
        $this->actor->wait(4);
        // 完了メッセージが出ている
        $this->actor->see('更新が完了しました', 'p');
        // ちゃんと元の画面に戻っている
        $this->actor->seeInTitle($this->menu->title);
        $this->actor->see($this->menu->title, 'h1');
        // モーダルが消えている
        $this->actor->cantSeeElement('#modal');
        // リロードすると完了メッセージが消えている
        $this->actor->reloadPage();
        $this->actor->wait(3);
        $this->actor->cantSee('更新が完了しました');
    }

    /**
     * @return string
     */
    public function getOptionName()
    {
        return str_replace(['/manage/secure/option-', '/list'], ['', ''], $this->menu->href);
    }

    /**
     * 項目説明文の表示・非表示、最大文字数の検査
     * @param bool $exists
     * @param int|bool $maxLength
     * @param bool $newLine
     */
    public function checkInputOfExplain($exists, $maxLength = false, $newLine = true)
    {
        if ($exists) {
            if ($maxLength) {
                // 文字数チェックアリ
                $this->actor->amGoingTo("項目説明文の入力は{$maxLength}文字以内");
                $over = str_repeat('a', $maxLength + 1);
                $just = $newLine ? str_repeat('a', 5) . PHP_EOL . str_repeat('a', $maxLength - 6) : str_repeat('a', $maxLength);

                $this->actor->fillField("#{$this->optionName}columnset-column_explain", $over);
                $this->actor->wait(1);
                $this->actor->see("項目説明文は{$maxLength}文字以下で入力してください。", '//tr[@id="updateForm-column_explain-tr"]/td/div/div');
                $this->actor->fillField("#{$this->optionName}columnset-column_explain", $just);
                $this->actor->wait(1);
                $this->actor->seeElement('//tr[@id="updateForm-column_explain-tr"]/td/div[contains(@class, "has-success")]');
            } else {
                // 文字数チェック省略
                $this->actor->expect('項目説明文入力が表示されている');
                $this->actor->seeElement("#{$this->optionName}columnset-column_explain");
            }
        } else {
            // 項目説明文非表示チェック
            $this->actor->expect('項目説明文入力が表示されてない');
            $this->actor->dontSee('項目説明文', 'label');
        }
    }

    /**
     * 項目説明文(氏)、項目説明文(名)が表示されている、最大文字数の検査
     * @param int $maxLength
     */
    public function checkInputOfFullName($maxLength)
    {
        $this->actor->amGoingTo("項目説明文の入力は{$maxLength}文字以内");
        $over = str_repeat('a', $maxLength + 1);
        $just = str_repeat('a', $maxLength);

        // 項目説明文(氏）の検証
        $this->actor->fillField("#{$this->optionName}columnset-columnexplainsei", $over);
        $this->actor->wait(1);
        $this->actor->see("項目説明文(氏)は{$maxLength}文字以下で入力してください。", '//tr[@id="updateForm-columnExplainSei-tr"]/td/div/div');
        $this->actor->fillField("#{$this->optionName}columnset-columnexplainsei", $just);
        $this->actor->wait(1);
        $this->actor->seeElement('//tr[@id="updateForm-columnExplainSei-tr"]/td/div[contains(@class, "has-success")]');

        // 項目説明文(名）の検証
        $this->actor->fillField("#{$this->optionName}columnset-columnexplainmei", $over);
        $this->actor->wait(1);
        $this->actor->see("項目説明文(名)は{$maxLength}文字以下で入力してください。", '//tr[@id="updateForm-columnExplainMei-tr"]/td/div/div');
        $this->actor->fillField("#{$this->optionName}columnset-columnexplainmei", $just);
        $this->actor->wait(1);
        $this->actor->seeElement('//tr[@id="updateForm-columnExplainMei-tr"]/td/div[contains(@class, "has-success")]');
    }

    /**
     * 入力方法選択により項目説明文の表示・非表示を検査
     * @param int|bool $maxLength
     */
    public function optionColumnExplain($maxLength = false)
    {
        $this->actor->amGoingTo('入力方法でチェックボックスを選択');
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_CHECK);
        $this->checkInputOfExplain(false);

        $this->actor->amGoingTo('入力方法でラジオボタンを選択');
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_RADIO);
        $this->checkInputOfExplain(false);

        $this->actor->amGoingTo('入力方法でテキストを選択');
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_TEXT);
        $this->checkInputOfExplain(true, $maxLength);

        $this->actor->amGoingTo('入力方法で数字を選択');
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_NUMBER);
        $this->checkInputOfExplain(true, $maxLength);

        $this->actor->amGoingTo('入力方法でメールアドレスを選択');
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_MAIL);
        $this->checkInputOfExplain(true, $maxLength);

        $this->actor->amGoingTo('入力方法でURLを選択');
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_URL);
        $this->checkInputOfExplain(true, $maxLength);
    }

    /**
     * 項目説明文に文字数上限エラーが出ている状態でオプションをテキストからチェックボックに変更する
     * @param int $maxLength
     */
    public function optionChangeDataType($maxLength)
    {
        $this->actor->amGoingTo('項目説明文に文字数上限エラーが出ている状態で入力方法を切り替えて項目説明文を隠し、選択肢項目名を入力');
        // 入力方法：テキスト選択、最大文字数+1を入力
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_TEXT);
        $over = str_repeat('a', $maxLength + 1);
        $this->actor->fillField("#{$this->optionName}columnset-column_explain", $over);
        $this->actor->wait(1);

        // 入力方法：チェックボックス選択、選択肢項目名を入力
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_CHECK);
        $this->actor->fillField("#{$this->optionName}columnsubset-0-subset_name", 'a');
        $this->actor->wait(1);
    }

    /**
     *  項目説明文に特殊文字が登録できるかを検査
     */
    public function chkInputSpecialCharacter()
    {
        $this->actor->amGoingTo('項目説明文に特殊文字を入力');
        $this->actor->fillField("#{$this->optionName}columnset-column_explain", '<>!”#$%&’()*+-./;:=?[]¥^');
        $this->actor->wait(1);
        $this->actor->seeElement('//tr[@id="updateForm-column_explain-tr"]/td/div[contains(@class, "has-success")]');
    }

    /**
     *  項目説明文（氏)、項目説明文（名）に特殊文字が登録できるかを検査
     */
    public function chkInputSpecialCharacterFullName()
    {
        $spcial = '<>!”#$%&’';

        // 項目説明文(氏）の検証
        $this->actor->amGoingTo('項目説明文（氏）に特殊文字を入力');
        $this->actor->fillField("#{$this->optionName}columnset-columnexplainsei", $spcial);
        $this->actor->wait(1);
        $this->actor->seeElement('//tr[@id="updateForm-columnExplainSei-tr"]/td/div[contains(@class, "has-success")]');

        // 項目説明文(名）の検証
        $this->actor->amGoingTo('項目説明文（名）に特殊文字を入力');
        $this->actor->fillField("#{$this->optionName}columnset-columnexplainmei", $spcial);
        $this->actor->wait(1);
        $this->actor->seeElement('//tr[@id="updateForm-columnExplainMei-tr"]/td/div[contains(@class, "has-success")]');
    }

    /**
     * 入力方法「チェックボックス（ラジオボタン）」で選択肢項目$max個のオプション項目を作成
     *
     * * @param int $max
     */
    public function createCheckBox($max)
    {
        $this->actor->wantTo('新規追加のテスト');
        $this->actor->amGoingTo('入力方法でチェックボックスを選択');
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_TEXT);
        $this->actor->wait(1);
        $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_CHECK);
        $this->actor->wait(1);

        --$max;
        $this->actor->amGoingTo("選択肢項目名を{$max}個追加");
        for ($i = 0; $i < $max; $i++) {
            $this->actor->wait(1);
            $this->actor->click('追加');
        }
        $this->actor->wait(1);

        $this->actor->amGoingTo('選択肢項目名に仮の値を入力');
        for ($i = 0; $i <= $max; $i++) {
            $this->actor->fillField("#{$this->optionName}columnsubset-{$i}-subset_name", $i);
        }
    }
    /**
     * 入力方法「チェックボックス（ラジオボタン）」選択時の、選択肢項目名のバリデーションを検査
     *   ※途中でクリック処理を挟んでいる所理由は、意図しないバリデーションが実行されるのをさけるため。
     *
     * * @param int $maxLength
     * * @param int $isNew
     */
    public function chkInputSubsetName($maxLength, $isNew)
    {
        $index = 0;
        if ($isNew) {
            $this->actor->wantTo('新規追加のテスト');
            $this->actor->amGoingTo('入力方法でチェックボックスを選択');
            $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_TEXT);
            $this->actor->wait(1);
            $this->actor->selectOption("#{$this->optionName}columnset-data_type", BaseColumnSet::DATA_TYPE_CHECK);
            $this->actor->wait(1);

            $this->actor->amGoingTo('選択肢項目名を3個追加');
            // どこでもいいのでクリック
            $this->actor->click("#subset_list");
            $this->actor->wait(1);
            $this->actor->click('追加');
            $this->actor->wait(1);
            // どこでもいいのでクリック
            $this->actor->click("#subset_list");
            $this->actor->wait(1);
            $this->actor->click('追加');
            $this->actor->wait(1);
            // どこでもいいのでクリック
            $this->actor->click("#subset_list");
            $this->actor->wait(1);
            $this->actor->click('追加');
            $this->actor->wait(1);

            $this->actor->amGoingTo('3つ目まで選択肢項目名に仮の値を入力');
            for ($i = 0; $i < 3; $i++) {
                $this->actor->fillField("#{$this->optionName}columnsubset-{$i}-subset_name", $i);
                $this->actor->wait(1);
                // どこでもいいのでクリック
                $this->actor->click("#subset_list");
                $this->actor->wait(2);
            }
        } else {
            $this->actor->wantTo('既存入力のテスト');
        }

        $this->actor->see('選択肢項目名', '//tr[@id="updateForm-subset_name-tr"]/th/div/label');

        $no = $index + 1;
        $this->actor->amGoingTo('選択肢項目名に最大文字数以上を入力');
        $this->actor->fillField("#{$this->optionName}columnsubset-{$index}-subset_name", str_repeat('a', $maxLength + 1));
        $this->actor->wait(1);
        $this->actor->see("選択肢項目名は{$maxLength}文字以下で入力してください。", "//tr[@id='updateForm-subset_name-tr']/td/div/ul/li[{$no}]/div/div[@class='error-block text-danger']");

        $this->actor->amGoingTo('選択肢項目名に正常な値を入力');
        $this->actor->fillField("#{$this->optionName}columnsubset-{$index}-subset_name", '東京');
        $this->actor->wait(1);
        // どこでもいいのでクリック
        $this->actor->click("#subset_list");
        $this->actor->wait(2);
        $this->actor->cantSee("選択肢項目名は{$maxLength}文字以下で入力してください。", "//tr[@id='updateForm-subset_name-tr']/td/div/ul/li[{$no}]/div/div[@class='error-block text-danger']");
        $this->actor->expect('選択肢項目名のラベルが緑にも赤にもなっていない');
        $this->actor->cantSeeElement('//tr[@id="updateForm-subset_name-tr"]/th/div[contains(@class, "has-success")]');
        $this->actor->cantSeeElement('//tr[@id="updateForm-subset_name-tr"]/th/div[contains(@class, "has-error")]');
        $index++;
        $no++;
        $this->actor->amGoingTo('選択肢項目名に重複した値を入力');
        $this->actor->fillField("#{$this->optionName}columnsubset-{$index}-subset_name", '東京');
        $this->actor->wait(1);
        // どこでもいいのでクリック
        $this->actor->click("#subset_list");
        $this->actor->wait(2);
        $this->actor->see("選択肢項目名が重複しています。", "//tr[@id='updateForm-subset_name-tr']/td/div/ul/li[{$no}]/div/div[@class='error-block text-danger']");
        $this->actor->expect('選択肢項目名のラベルが赤になっている');
        $this->actor->cantSeeElement('//tr[@id="updateForm-subset_name-tr"]/th/div[contains(@class, "has-success")]');
        $this->actor->seeElement('//tr[@id="updateForm-subset_name-tr"]/th/div[contains(@class, "has-error")]');
        $this->actor->amGoingTo('選択肢項目名をエラー値から正常値へ変更');
        $this->actor->fillField("#{$this->optionName}columnsubset-{$index}-subset_name", '京都');
        $this->actor->wait(1);
        // どこでもいいのでクリック
        $this->actor->click("#subset_list");
        $this->actor->wait(2);
        $this->actor->cantSee("選択肢項目名が重複しています。", "//tr[@id='updateForm-subset_name-tr']/td/div/ul/li[{$no}]/div/div[@class='error-block text-danger']");
        $this->actor->expect('選択肢項目名のラベルが緑にも赤にもなっていない');
        $this->actor->cantSeeElement('//tr[@id="updateForm-subset_name-tr"]/th/div[contains(@class, "has-success")]');
        $this->actor->cantSeeElement('//tr[@id="updateForm-subset_name-tr"]/th/div[contains(@class, "has-error")]');
        $index++;
        $this->actor->fillField("#{$this->optionName}columnsubset-{$index}-subset_name", '大阪');
        $this->actor->wait(1);
        // どこでもいいのでクリック
        $this->actor->click("#subset_list");
        $this->actor->wait(2);

        if ($isNew) {
            // 適当な値を最後の選択肢項目に入れてエラーが出ないようにする。
            $index++;
            $this->actor->fillField("#{$this->optionName}columnsubset-{$index}-subset_name", $index + 1);
            $this->actor->wait(1);
            // どこでもいいのでクリック
            $this->actor->click("#subset_list");
            $this->actor->wait(2);

            $this->actor->expect('選択肢項目名のラベルが緑になっている');
            $this->actor->seeElement('//tr[@id="updateForm-subset_name-tr"]/th/div[contains(@class, "has-success")]');
            $this->actor->cantSeeElement('//tr[@id="updateForm-subset_name-tr"]/th/div[contains(@class, "has-error")]');
        }
    }
}
