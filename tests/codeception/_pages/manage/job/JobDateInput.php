<?php

namespace tests\codeception\_pages\manage\job;

use app\models\manage\ClientChargePlan;
use WebDriverKeys;
use yii\codeception\BasePage;

/**
 * Represents login page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class JobDateInput extends BasePage
{
    public $route = '/manage/secure/job/create';
    /** @var string 開始・終了日時のformに現在入力されている値 */
    public $startDate;
    public $endDate;
    /** @var ClientChargePlan 選択されているプランのインスタンス */
    public $plan;

    /**
     * 入力後、エラーをチェックした後にフォーカスを外してinputを書き換えてエラーが消えていることをチェックする
     * @param $error
     */
    public function checkStartInvalid($error)
    {
        $this->actor->see($error);
        // フォーカスを外すと、不正な文字列の際は今日の日付が入り、範囲外の日付の場合は空白が入る
        $this->actor->selectOption('input[name=JobMaster\\[valid_chk\\]]', 1);
        // エンターを押すとどちらの場合も今日の日付が入る（testはしないが、コメントアウトで残しておく）
//        $this->actor->pressKey('#jobmaster-disp_start_date', WebDriverKeys::ENTER);
        $this->actor->wait(2);
        $this->actor->cantSee($error);
    }

    /**
     * 入力後、エラーをチェックした後にエンターを押してinputを書き換えてエラーが消えていることをチェックする
     * @param $error
     */
    public function checkEndInvalid($error)
    {
        $this->actor->see($error);
        // フォーカスを外すと、不正な文字列の際は今日の日付が入り、範囲外の日付の場合は空白が入る
        $this->actor->selectOption('input[name=JobMaster\\[valid_chk\\]]', 1);
        // エンターを押すとどちらの場合も今日の日付が入る（testはしないが、コメントアウトで残しておく）
//        $this->actor->pressKey('#jobmaster-disp_end_date', WebDriverKeys::ENTER);
        $this->actor->wait(2);
        $this->actor->cantSee($error);
    }

    /**
     * 開始日のinputに任意の値を入力して、現在の値をキャッシュする
     * @param $value
     */
    public function fillStart($value)
    {
        if ($this->startDate !== $value) {
            // 一回フォーカス外さないとなぜか入力を受け付けない場合があるのでフォーカスを外している
            $this->actor->selectOption('input[name=JobMaster\\[valid_chk\\]]', 1);
            $this->actor->wait(1);
            $this->actor->fillField('#jobmaster-disp_start_date', $value);
            $this->startDate = $value;
            $this->actor->wait(1);
        }
    }

    /**
     * 終了日のinputに任意の値を入力して、現在の値をキャッシュする
     * @param $value
     */
    public function fillEnd($value)
    {
        if ($this->endDate !== $value || isset($this->plan->period)) {
            // 一回フォーカス外さないとなぜか入力を受け付けないのでフォーカスを外している
            $this->actor->selectOption('input[name=JobMaster\\[valid_chk\\]]', 1);
            $this->actor->wait(1);
            $this->actor->fillField('#jobmaster-disp_end_date', $value);
            $this->endDate = $value;
            $this->actor->wait(1);
        }
    }

    /**
     * プランを変えて現在の値をキャッシュする
     * @param $plan
     */
    public function changePlan($plan)
    {
        if ($plan instanceof ClientChargePlan) {
            $this->actor->selectOption('#jobmaster-client_charge_plan_id', $plan->id);
            $this->plan = $plan;
        } else {
            $this->actor->selectOption('#jobmaster-client_charge_plan_id', '');
            $this->plan = null;
        }
        $this->actor->wait(1);
    }

    /**
     * 開始日のinputが任意の値であることを確認して、現在の値を更新する
     * @param $value
     */
    public function seeStart($value)
    {
        $this->actor->canSeeInField('#jobmaster-disp_start_date', $value);
        $this->startDate = $value;
    }

    /**
     * 終了日のinputが任意の値であることを確認して、現在の値を更新する
     * @param $value
     */
    public function seeEnd($value)
    {
        $this->actor->canSeeInField('#jobmaster-disp_end_date', $value);
        $this->endDate = $value;
    }

    /**
     * @param $date string
     * @param $days int
     * @return bool|string
     */
    public static function addDays($date, $days)
    {
        return date('Y/m/d', strtotime($date) + 60 * 60 * 24 * $days);
    }

    /**
     * 開始日も終了日もエラーが無いことを確認する
     */
    public function dateHasNoError()
    {
        $this->startHasNoError();
        $this->endHasNoError();
    }

    /**
     * 開始日にエラーが無いことを確認する
     */
    public function startHasNoError()
    {
        $this->actor->cantSeeElement('div.field-jobmaster-disp_start_date.has-error');
    }

    /**
     * 終了日にエラーが無いことを確認する
     */
    public function endHasNoError()
    {
        $this->actor->cantSeeElement('div.field-jobmaster-disp_end_date.has-error');
    }

    /**
     * 両方に正常な日付（今日の日付）を入力する
     */
    public function setToday()
    {
        $this->fillStart(date('Y/m/d'));
        $this->fillEnd(date('Y/m/d'));
    }

    /**
     * 開始日に明日、終了日に今日の日付を入力して、比較エラーを出す
     */
    public function setCompareError()
    {
        $this->fillStart(self::addDays(date('Y/m/d'), 1));
        $this->fillEnd(date('Y/m/d'));
    }

    /**
     * 終了日のみ入っている状態
     */
    public function onlyEnd()
    {
        $this->fillStart('');
        $this->fillEnd(date('Y/m/d'));
    }

    /**
     * 終了日が初期表示状態に戻っていることを検証
     */
    public function cantSeeEnd()
    {
        // inputが見えない
        $this->actor->cantSee('#dispEndText');
        // エラー文が見えない
        $this->actor->cantSeeElement('.field-jobmaster-disp_end_date .error-block');
        // validateアイコンが見えない
        $this->actor->cantSeeElement('.field-jobmaster-disp_end_date .glyphicon-remove');
        $this->actor->cantSeeElement('.field-jobmaster-disp_end_date .glyphicon-ok');
        // inputやlabelが赤にも緑にもなっていない
        $this->actor->cantSeeElement('.field-jobmaster-disp_end_date.has-error');
        $this->actor->cantSeeElement('.field-jobmaster-disp_end_date.has-success');
    }
}
