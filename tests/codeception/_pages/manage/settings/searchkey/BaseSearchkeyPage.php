<?php

namespace tests\codeception\_pages\manage\settings\searchkey;

use app\models\manage\SearchkeyMaster;
use tests\codeception\_pages\manage\BaseGridPage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class BaseSearchkeyPage extends BaseGridPage
{
    const CREATE = 0;
    const UPDATE = 1;
    const UPDATE2 = 2;

    public $route = '/manage/secure/settings/list';
    /** @var SearchkeyMaster */
    public $searchKey;

    /**
     * サイト設定画面から項目設定画面へ遷移する
     * その際にmenuも代入する
     * @param SearchkeyMaster $searchKey
     */
    public function go($searchKey)
    {
        $this->actor->amGoingTo("{$searchKey->searchkey_name}へ遷移");
        $this->searchKey = $searchKey;
        $this->actor->click("//h4[contains(text(), '$searchKey->searchkey_name')]"); // CSSセレクタでやると厄介そうなのでXPath使ってます
        $this->actor->wait(3);
        $this->actor->seeInTitle($searchKey->searchkey_name);
        $this->actor->see($searchKey->searchkey_name, 'h1');
    }

    /**
     * 登録・変更用モーダルが正常に開けることを検査
     * @param $link
     * @param $action
     * @throws \Exception
     */
    public function openModal($link, $action)
    {
        $this->actor->amGoingTo('入力モーダルを開く');
        $this->actor->click($link);
        $this->actor->wait(1);
        switch ($action) {
            case self::CREATE:
                $this->actor->see('登録', 'div.modal-header');
                $this->actor->cantSee('削除', 'div.modal-footer');
                break;
            case self::UPDATE:
                $this->actor->see('変更', 'div.modal-header');
                $this->actor->see('削除', 'div.modal-footer');
                break;
            case self::UPDATE2:
                $this->actor->see('変更', 'div.modal-header');
                $this->actor->cantSee('削除', 'div.modal-footer');
                break;
            default :
                throw new \Exception('open actionが不正です');
                break;
        }
    }

    /**
     * モーダルを閉じる
     */
    public function closeModal()
    {
        $this->actor->amGoingTo('入力モーダルを閉じる');
        $this->actor->click('×');
        $this->actor->wait(1);
    }

    /**
     * モーダルのsubmitが正常に行われていることを検査
     * @param $action
     * @throws \Exception
     */
    public function submitModal($action)
    {
        $this->actor->amGoingTo('入力モーダルをsubmitする');
        switch ($action) {
            case self::CREATE:
                // 登録をクリック
                $this->actor->click('登録');
                $this->actor->wait(3);
                // 完了メッセージが出ている
                $this->actor->see('登録が完了しました。', 'p');
                break;
            case self::UPDATE:
                // 変更をクリック
                $this->actor->click('変更');
                $this->actor->wait(3);
                // 完了メッセージが出ている
                $this->actor->see('更新が完了しました。', 'p');
                break;
            default :
                throw new \Exception('submit actionが不正です');
                break;
        }
        // ちゃんと元の画面に戻っている
        $this->actor->seeInTitle($this->searchKey->searchkey_name);
        $this->actor->see($this->searchKey->searchkey_name, 'h1');
        // モーダルが消えている
        $this->actor->cantSeeElement('.fade.modal');
    }

    /**
     * モーダルの削除ボタンを押して完了メッセージをチェックする
     */
    public function delete()
    {
        $this->actor->amGoingTo('削除する');
        $this->actor->click('削除', 'div.modal-footer');
        $this->actor->wait(3);
        $this->actor->see('削除したものは元に戻せません。削除しますか？');
        $this->actor->click('OK');
        $this->actor->wait(3);
        $this->actor->see('削除が完了しました', 'p');
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
