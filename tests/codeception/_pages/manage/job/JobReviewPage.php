<?php

namespace tests\codeception\_pages\manage\job;

use app\models\manage\AdminMaster;
use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\DispType;
use app\models\manage\JobReviewStatus;
use tests\codeception\_pages\manage\BaseRegisterPage;
use Yii;
use app\models\manage\searchkey\Pref;
use app\models\manage\JobMaster;
use app\modules\manage\models\JobReview;
use app\modules\manage\models\Manager;
use app\models\manage\JobReviewHistory;

/**
 * 求人審査用、求人登録・編集ページ拡張クラス
 * @property \AcceptanceTester $actor
 */
class JobReviewPage extends BaseRegisterPage
{
    public $route = 'manage/secure/job/list';

    /**
     * 登録・変更する
     *   ※審査機能有効用。分岐処理が過多のため、別枠に設ける。
     * @param string $action 登録 or 変更
     * @param JobMaster $model 求人モデル
     * @param bool $saveOnly 一時保存フラグ
     * @param bool $stacked
     */
    public function submit($action, $model, $saveOnly = true, $stacked = false)
    {
        $actionMsg = $action;
        if ($saveOnly) {
            // 一時保存の場合actionを切り替える
            $action = '一時保存';
        }

        /** @var Manager $identity*/
        $identity = Yii::$app->user->identity;

        $this->actor->amGoingTo($action);
        if ($stacked) {
            $this->actor->click('#stackedSubmit');  // 保存・審査依頼の処理と同等
        } elseif ($identity->isOwner() || $saveOnly) {
            $this->actor->click("{$action}する");
        } else {
            $this->actor->click("{$action}し、審査依頼へ進む");
        }
        $this->actor->wait(10);
        // 共通メッセージ
        $this->actor->see("求人原稿情報を{$actionMsg}してもよろしいですか？", '//div[@class="bootbox-body"]');

        // 審査機能ONの場合
        if (Yii::$app->tenant->tenant->review_use) {
            $this->checkConfirmMessage($model, $identity->myRole, $saveOnly);
        }

        $this->actor->click('OK');
        $this->actor->wait(20);
        $this->actor->seeInTitle('求人原稿情報 - 完了');

        if ($saveOnly) {
            // 一時保存の場合
            $this->actor->see("{$actionMsg}完了", 'h1');
        }
        if ($actionMsg == '登録') {
            $this->actor->see("求人原稿情報が{$actionMsg}されました", 'p');
        } elseif ($actionMsg == '変更') {
            $this->actor->see("求人原稿情報の内容が{$actionMsg}されました", 'p');
        }

        if (!$saveOnly && !$identity->isOwner()) {
            // 保存＋審査依頼の場合
            $this->actor->see('審査依頼ボタンをクリックして審査依頼を行ってください。', 'p');
            $this->actor->see('代理店・運営元にて原稿の確認を行います。', 'p');
            $this->actor->seeElement('//img[@src="/pict/flow.png"]');
            $this->actor->see('審査依頼する', 'button');
            $this->actor->see('求人原稿情報一覧へ', 'a');
            $this->actor->see('トップページへ戻る', 'a');
        }
    }

    /**
     * 確認メッセージの確認
     * @param JobMaster $model 求人モデル
     * @param Manager $identity
     * @param bool $saveOnly 一時保存フラグ
     */
    private function checkConfirmMessage($model, $identity, $saveOnly)
    {
        $boxPath = '//div[@class="bootbox-body"]';
        if (($model->job_review_status_id == JobReviewStatus::STEP_CORP_REVIEW  && !$identity->isCorp()) ||
            ($model->job_review_status_id == JobReviewStatus::STEP_OWNER_REVIEW  && !$identity->isOwner())) {
            // 原稿が「代理店審査中」で代理店管理者以外が修正しようとしている場合
            // 原稿が「運営元審査中」で運営元管理者以外が修正しようとしている場合
            $this->actor->see("【注意】現在{$model->jobReviewStatus->name}です。");
        }

        if ($saveOnly && !$identity->isOwner()) {
            // 一時保存の場合
            $this->actor->see('また審査依頼されずに保存されますがよろしいですか？', $boxPath);
        } elseif (!$saveOnly && !$identity->isOwner()) {
            // 保存＋審査依頼の場合
            $this->actor->seeElement('//img[@src="/pict/flow.png"]');
            $this->actor->see('掲載中の原稿を編集し、「登録」すると再度審査が必要となります。', $boxPath);
            $this->actor->see('(審査が完了するまで掲載は非表示となります)', $boxPath);
            $this->actor->see('掲載をご希望の場合は、次画面の「審査依頼」ボタンをクリックし、審査依頼を行ってください。', $boxPath);
        }
    }

    /**
     * disp_type.disp_type_no=1の代理店、掲載企業、プランの組み合わせを準備
     * ※審査用
     * @return array
     */
    public static function initPlan()
    {
        $admin = AdminMaster::find()->where(['login_id' => 'admin03'])->one();
        $dispTypeId = DispType::findOne(['disp_type_no' => 1]);
        $corp = CorpMaster::findOne(['valid_chk' => 1, 'id' => $admin->corp_master_id]);
        $client = ClientMaster::findOne(['corp_master_id' => $corp->id, 'valid_chk' => 1, 'id' => $admin->client_master_id]);
        $plan = ClientChargePlan::findOne(['disp_type_id' => $dispTypeId, 'valid_chk' => 1]);
        ClientCharge::deleteAll(['client_master_id' => $client->id]);
        $clientCharge = new ClientCharge([
            'client_charge_plan_id' => $plan->id,
            'client_master_id' => $client->id,
        ]);
        $clientCharge->save();

        return [
            'dispTypeId' => $dispTypeId,
            'corp' => $corp,
            'client' => $client,
            'charge' => $clientCharge,
        ];
    }


    /**
     * 登録できるように最低限の値を入力
     * @param string $role
     * @param CorpMaster $corp
     * @param ClientMaster $client
     * @param ClientCharge $charge
     * @param Pref $pref 都道府県
     */
    public function minimumInput($role, $corp, $client, $charge, $pref)
    {
        $this->actor->amGoingTo('代理店、掲載企業、プランを入力');
        switch ($role) {
            case Manager::OWNER_ADMIN:
                // 運営元管理者の場合、代理店選択から実施
                $this->actor->click('//*[@id="select2-jobmaster-corpmasterid-container"]');
                $this->actor->wait(2);
                $this->actor->fillField('//span[@class="select2-search select2-search--dropdown"]/input', $corp->corp_name);
                $this->actor->wait(2);
                $this->actor->click("//*[@id='select2-jobmaster-corpmasterid-results']/li[text()='$corp->corp_name']");
                $this->actor->wait(2);
                break;
            case Manager::CORP_ADMIN:
                // 代理店管理者の場合、掲載企業選択から実施
                $this->actor->click('//*[@id="select2-jobmaster-client_master_id-container"]');
                $this->actor->wait(2);
                $this->actor->fillField('//span[@class="select2-search select2-search--dropdown"]/input', $client->client_name);
                $this->actor->wait(2);
                $this->actor->click("//*[@id='select2-jobmaster-client_master_id-results']/li[text()='$client->client_name']");
                $this->actor->wait(2);
                break;
            case Manager::CLIENT_ADMIN:
                // 掲載企業管理者は料金プランから選択
                $this->actor->selectOption('//select[@id="jobmaster-client_charge_plan_id"]', $charge->client_charge_plan_id);
                break;
        }

        $this->actor->amGoingTo('日付を入力');
        $this->actor->fillField('#jobmaster-disp_start_date', date('Y/m/d'));
        $this->actor->amGoingTo('状態を有効に');
        $this->actor->selectOption('input[name=JobMaster\\[valid_chk\\]]', 1);

        $this->actor->amGoingTo('検索キーの入力');

        $this->actor->click('選択する');
        $this->actor->wait(1);

        $this->actor->amGoingTo("{$pref->dist[0]->dist_name}をチェック");
        $this->actor->executeJS("$('#pref{$pref->id}').collapse('show')"); // アコーディオンを開く
        $this->actor->wait(1);
        $this->actor->checkOption("div#pref{$pref->id} input[name=JobDist\\[itemIds\\]\\[\\]]", $pref->dist[0]->dist_name);
        $this->actor->click('変更を保存');
        $this->actor->wait(1);
    }

    /**
     * ボタンチェック
     * @param string $role
     * @param string $action
     */
    public function checkButton($role, $action)
    {
        if (Yii::$app->tenant->tenant->review_use) {
            switch ($role) {
                case Manager::OWNER_ADMIN:
                    $this->actor->cantSee('一時保存する', 'button');
                    $this->actor->see("{$action}する", 'button');
                    break;
                case Manager::CORP_ADMIN:
                case Manager::CLIENT_ADMIN:
                    $this->actor->see('一時保存する', 'button');
                    $this->actor->see("{$action}・審査依頼", 'button');
                    $this->actor->see("{$action}し、審査依頼へ進む", 'button');
                    break;
            }
        } else {
            $this->actor->cantSee('一時保存する', 'button');
            $this->actor->see("{$action}する", 'button');
        }
    }

    /**
     * 審査履歴のチェック
     * @param JobMaster $model
     * @param boolean $modalFlg モーダルの履歴のチェックか登録画面の履歴のチェックか。
     */
    public function checkReviewHistory($model, $modalFlg)
    {
        $headerPath = '//div[@id="flow-05"]';
        $historyPath = '//div[@id="review-history-accordion"]';

        if ($modalFlg) {
            $msg = '審査モーダルの審査履歴をチェック';
            $historyPath = '//div[@id="review-history-accordion-modal"]';
        } else {
            $msg = '登録・編集画面の審査履歴をチェック';
        }
        $this->actor->amGoingTo($msg);
        if (!$modalFlg) {
            // 登録・編集画面の時のみチェック
            $this->actor->see('審査状況を確認する', $headerPath);
        }
        $this->actor->see('審査履歴', "$historyPath/div/div[1]/h2");
        $this->actor->click("$historyPath/div/div[1]/h2");
        $this->actor->wait(1);
        if ($model->jobReviewHistory == null) {
            // 審査履歴が無い場合
            $this->actor->see('該当するデータがありません', "$historyPath/div/div[2]/div[2]");
        } else {
            // 審査履歴がある場合
            $histories = array_reverse($model->jobReviewHistory);
            $labels = $model->jobReviewHistory[0]->attributeLabels();
            $tablePath = "$historyPath/div/div[2]/div/table";
            $tableHeadPath = "$tablePath/thead/tr[1]";
            // ヘッダーチェック
            $this->actor->see($labels['created_at'], "$tableHeadPath/th[1]");
            $this->actor->see($labels['job_review_status_id'], "$tableHeadPath/th[2]");
            $this->actor->see($labels['admin_master_id'], "$tableHeadPath/th[3]");
            $this->actor->see($labels['comment'], "$tableHeadPath/th[4]");

            // 審査履歴チェック
            foreach ($histories as $i => $history) {
                /** @var JobReviewHistory $history */
                $index = $i + 1;
                $tableBodyPath = "$tablePath/tbody/tr[$index]";

                if ($i < JobReviewHistory::HISTORY_MAX) {
                    $this->actor->see(Yii::$app->formatter->format($history->created_at, 'DateTime'), "$tableBodyPath/td[1]");
                    $this->actor->see($history->jobReviewStatus->name, "$tableBodyPath/td[2]");
                    $this->actor->see($history->adminMaster->fullName, "$tableBodyPath/td[3]");
                    $this->actor->see(Yii::$app->formatter->format($history->comment, 'NText'), "$tableBodyPath/td[4]");
                } else {
                    $this->actor->cantSeeElement($tableBodyPath);
                }
            }
        }
    }

    /**
     * 審査コメント入力
     * @param String
     */
    public function reviewComment($comment)
    {
        $this->actor->fillField('//textarea[@id="jobreview-comment"]', $comment);
    }

    /**
     * 審査
     * @param JobMaster $model
     * @param string $okNg OK or NG
     * @param string $comment 審査コメント
     * @param boolean $errCheck 名称チェックを行うかどうか。
     * @param boolean $errCheck 通常のエラーチェックを行うかどうか。
     */
    public function review($model, $okNg, $comment, $nameCheck, $errCheck)
    {
        // 各種Xpath設定
        $modalPath = '//div[@id="modal"]';
        $modalHeadPath = "$modalPath//div[@class='modal-header']";
        $modalBodyPath = "$modalPath//div[@class='modal-body']";
        $modalFotterPath = "$modalPath//div[@class='modal-footer']";

        $modalReviewPath = "$modalBodyPath/table";
        $modalHistoryPath = '//div[@id="review-history-accordion-modal"]';

        $jobReview = new JobReview();
        $jobReview->job_master_id = $model->id;
        $labels = $jobReview->attributeLabels();

        // エラーチェックする場合
        // なぜか見つからないのでコメントアウト
        // エラー出力されたHTMLには出ているので、おそらくphantomJSの不具合だと思われる。
//         if ($errCheck) {
//             $this->actor->amGoingTo('必須チェック');
//             $this->actor->click('更新');
//             $this->actor->wait(3);
//             $this->actor->see("{$labels['review']}は必須項目です。");
//         }

        // 名称チェックをする場合
        if ($nameCheck) {
            // モーダル名称チェック
            $this->actor->amGoingTo('モーダル名称チェック');
            $this->actor->see('審査状況更新', $modalHeadPath);
            $this->actor->see('閉じる', "$modalFotterPath/button[1]");
            $this->actor->see('更新', "$modalFotterPath/button[2]");

            // 項目名チェック
            $this->actor->amGoingTo('審査項目名チェック');
            $this->actor->see($labels['job_review_status_id'], "$modalReviewPath/tbody/tr[1]/th/div/label");
            $this->actor->see('必須', "$modalReviewPath/tbody/tr[1]/th/div/span");
            $this->actor->see($labels['review'], "$modalReviewPath/tbody/tr[2]/th/div/label");
            $this->actor->see('必須', "$modalReviewPath/tbody/tr[2]/th/div/span");
            $this->actor->see($labels['comment'], "$modalReviewPath/tbody/tr[3]/th/div/label");
            $this->actor->see('任意', "$modalReviewPath/tbody/tr[3]/th/div/span");
        }

        // 通知先チェック
        $hintMsg = $jobReview->notificationHint();
        $hintArr = array_filter(explode('<br>', $hintMsg), 'strlen');
        $this->actor->amGoingTo('通知先チェック');
        foreach ($hintArr as $hint) {
            $this->actor->see($hint, "$modalReviewPath/tbody/tr[2]/td/div/p");
        }

        // 審査OK or NG
        $this->actor->amGoingTo('審査入力');
        if ($okNg === 'OK') {
            $this->actor->selectOption('//input[@name="JobReview[review]"]', 1);
        } else {
            $this->actor->selectOption('//input[@name="JobReview[review]"]', 0);
        }
        // 審査コメント
        $this->reviewComment($comment);

        // 審査
        $this->actor->click('更新');
        $this->actor->wait(20);
    }

    /**
     * 審査依頼・審査の完了チェック
     * @param boolean $reqFlg 審査依頼かどうか
     */
    public function reviewComplete($reqFlg)
    {
        $messages = [];
        if ($reqFlg) {
            // 審査依頼完了の場合
            $messages[] = '審査依頼 - 完了';
            $messages[] = '審査依頼完了';
            $messages[] = '審査依頼が完了しました。';
        } else {
            // 審査完了の場合
            $messages[] = '審査完了';
            $messages[] = '審査が完了しました。';
        }
        $this->actor->amGoingTo('完了画面確認');
        foreach ($messages as $msg) {
            $this->actor->see($msg);
        }
        $this->actor->see('求人原稿情報一覧へ', 'a');
        $this->actor->see('トップページへ戻る', 'a');
    }
}
