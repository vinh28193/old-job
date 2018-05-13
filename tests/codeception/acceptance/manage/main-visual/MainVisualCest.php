<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\Manager;
use tests\codeception\_pages\manage\ManageLoginPage;

/**
 * Class MainVisualCest
 */
class MainVisualCest
{
    /**
     * メインビジュアルページに管理者でアクセスする
     *
     * @param AcceptanceTester $I
     */
    public function ensureAccess(AcceptanceTester $I)
    {
        // メニュー情報取得
        $menu = ManageMenuMain::findFromRoute('/manage/secure/main-visual/form');

        $I->wantTo('メインビジュアル設定ページにアクセスする');

        /** @var Manager $manager */
        $manager = Manager::findIdentity(1);
        Yii::$app->user->identity = $manager;
        $loginPage = ManageLoginPage::openBy($I);
        $loginPage->login('admin01', 'admin01');
        $I->wait(1);

        $I->amOnPage('manage/secure/main-visual/form');
        $I->seeInTitle($menu->title);
        $I->see('メインビジュアル設定');

        $areaIds = explode(',', $I->grabAttributeFrom('#main-visual-container', 'data-areas'));

        foreach ($areaIds as $areaId) {
            // タブが切り替わることのチェック
            $I->click('#tab-label-' . $areaId . ' > a');
            $I->seeElement('#tab-panel-' . $areaId . '.tab-pane.active');
        }
    }

    /**
     * バリデーションチェック
     *
     * @param AcceptanceTester $I
     */
    public function ensureValidation(AcceptanceTester $I)
    {
        $I->wantTo('メインビジュアルのバリデーションをチェックする');

        Yii::$app->user->identity = Manager::findIdentity(1);
        $loginPage = ManageLoginPage::openBy($I);
        $loginPage->login('admin01', 'admin01');
        $I->wait(1);

        $I->amOnPage('manage/secure/main-visual/form');
        $I->wait(1);

        $I->fillField('#mainvisualimageform0_0-url', 'aaaaaa');
        $I->wait(1);
        $I->see(
            'PCリンク先URLは有効な URL 書式ではありません。',
            '#images-0_0 > table > tbody > tr:nth-child(2) > td > div > div'
        );

        $I->fillField('#mainvisualimageform0_0-url', 'http://google.co.jp/' . str_repeat('a', 237));
        $I->wait(1);
        $I->see(
            'PCリンク先URLは256文字以下で入力してください。',
            '#images-0_0 > table > tbody > tr:nth-child(2) > td > div > div'
        );

        $I->fillField('#mainvisualimageform0_0-url_sp', 'aaaaaa');
        $I->wait(1);
        $I->see(
            'スマホリンク先URLは有効な URL 書式ではありません。',
            '#images-0_0 > table > tbody > tr:nth-child(4) > td > div > div'
        );

        $I->fillField('#mainvisualimageform0_0-url_sp', 'http://google.co.jp/' . str_repeat('a', 237));
        $I->wait(1);
        $I->see(
            'スマホリンク先URLは256文字以下で入力してください。',
            '#images-0_0 > table > tbody > tr:nth-child(4) > td > div > div'
        );

        $I->fillField('#mainvisualimageform0_0-content', str_repeat('a', 65));
        $I->wait(1);
        $I->see(
            'altテキストは64文字以下で入力してください。',
            '#images-0_0 > table > tbody > tr:nth-child(5) > td > div > div'
        );
    }

    /**
     * メインビジュアル画像を設定する
     *
     * @param AcceptanceTester $I
     * @return bool
     */
    public function ensureAddMainVisual(AcceptanceTester $I)
    {
        // 開発や確認環境で画像がアップロード出来ないのでテスト不可
        // アップ可能になった後にテストシナリオを追加する
        $I->wantTo('メインビジュアルを設定する');
        return true;
    }
}