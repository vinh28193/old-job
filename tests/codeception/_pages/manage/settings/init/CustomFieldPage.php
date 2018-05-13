<?php

namespace tests\codeception\_pages\manage\settings\init;

use app\models\manage\ManageMenuMain;
use tests\codeception\_pages\manage\BaseGridPage;
use RemoteWebDriver;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class CustomFieldPage extends BaseGridPage
{
    public $route = '/manage/secure/settings/list';
    /** @var ManageMenuMain */
    public $menu;

    /**
     * サイト設定画面から項目設定画面へ遷移する
     * その際にmenuも代入する
     * @param ManageMenuMain $menu
     */
    public function go($menu)
    {
        $this->actor->amGoingTo("{$menu->title}へ遷移");
        $this->menu = $menu;
        $this->actor->wait(2);
        $this->actor->click("//h4[contains(., \"{$menu->title}\")]");
        $this->actor->wait(3);
        $this->actor->seeInTitle($menu->title);
        $this->actor->see($menu->title, 'h1');
    }

    /**
     * 新規登録モーダルを表示する
     */
    public function openCreateModal()
    {
        $this->actor->click('カスタムフィールドを登録する');
        $this->actor->wait(2);
        $this->actor->see('カスタムフィールド登録', 'div.modal-header');
        $this->actor->amGoingTo('新規モーダルはフィールドが空');
        $this->actor->seeInFormFields('form[id=form]', [
            'CustomField[detail]' => '',
            'CustomField[url]' => '',
            'CustomField[pict]' => '',
            'CustomField[valid_chk]' => '',
        ]);
    }

    /**
     * n行目の変更ボタンを押してモーダルを表示する
     * @param int $row
     */
    public function openModal($row)
    {
        $this->clickActionColumn($row, 1);
        $this->actor->wait(1);
        $this->actor->see('カスタムフィールド変更', 'div.modal-header');
    }

    /**
     * モーダルを閉じる
     */
    public function closeModal()
    {
        // 閉じるクリック
        $this->actor->click('閉じる');
        $this->actor->wait(1);
    }

    /**
     * 更新モーダルのsubmitが正常に行われていることを検査
     */
    public function submitUpdateModal()
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
     * 新規登録モーダルのsubmitが正常に行われていることを検査
     */
    public function submitCreateModal()
    {
        // 変更をクリック
        $this->actor->click('登録');
        $this->actor->wait(4);
        // 完了メッセージが出ている
        $this->actor->see('登録が完了しました', 'p');
        // ちゃんと元の画面に戻っている
        $this->actor->seeInTitle($this->menu->title);
        $this->actor->see($this->menu->title, 'h1');
        // モーダルが消えている
        $this->actor->cantSeeElement('#modal');
        // リロードすると完了メッセージが消えている
        $this->actor->reloadPage();
        $this->actor->wait(3);
        $this->actor->cantSee('登録が完了しました');
    }

    /**
     * まとめて削除で削除ができることを検査
     */
    public function deleteBulk()
    {
        $this->actor->see('選択中', 'div.check-fig');
        // まとめて削除するボタンをクリック
        $this->actor->click('まとめて削除する');
        $this->actor->wait(1);
        // 確認モーダル表示される
        $this->actor->see('削除したものは元に戻せません。削除しますか？');
        $this->actor->click('OK');
        $this->actor->wait(1);
        // すべて削除されたため1行も存在しない
        $this->actor->cantSeeElement('tr[data-key]');
        $this->actor->see('該当するデータがありません');
    }

    /**
     * 元のCSV一括登録のwindowを開く処理（window.nameが取得できないため下記処理にしている）
     */
    public function openWindow()
    {
        $this->actor->executeInSelenium(function (RemoteWebDriver $webDriver) {
            $handles = $webDriver->getWindowHandles();
            $firstWindow = reset($handles); // CSV一括登録のwindow
            $webDriver->switchTo()->window($firstWindow);
        });
    }

    /**
     * 指定文字列でURL検索する
     * @param $url
     */
    public function searchUrl($url)
    {
        // 検索URLを入力する
        $this->actor->fillField('CustomFieldSearch[url]', $url);
        $this->actor->click('この条件で表示する');
        $this->actor->wait(1);
    }

    /**
     *　検索結果をクリアする
     */
    public function clearSearchUrl()
    {
        $this->actor->click('クリア');
        $this->actor->wait(1);
    }

    /**
     * 指定した文字列でURL検索した結果を削除する
     * @param $url
     */
    public function deleteExistUrl($url)
    {
        $this->searchUrl($url);
        $length = $this->actor->executeJS("return $(':contains(まとめて削除する):last:enabled').length");
        if ($length > 0) {
            $this->deleteBulk();
        }
        $this->clearSearchUrl();
    }
}
