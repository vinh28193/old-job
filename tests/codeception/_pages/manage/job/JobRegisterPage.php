<?php

namespace tests\codeception\_pages\manage\job;

use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use app\models\manage\CorpMaster;
use app\models\manage\DispType;
use app\models\manage\JobColumnSet;
use Facebook\WebDriver\WebDriverKeys;
use tests\codeception\_pages\manage\BaseRegisterPage;
use Yii;
use yii\helpers\Html;

/**
 * Represents about page
 * @property \AcceptanceTester $actor
 */
class JobRegisterPage extends BaseRegisterPage
{
    public $route = 'manage/secure/job/list';

    /**
     * 登録・変更する
     * todo まとめられそうならBaseRegisterにまとめる
     * @param string $action 登録 or 変更
     * @param bool $stacked
     */
    public function submit($action, $stacked = false)
    {
        $this->actor->amGoingTo($action);
        if ($stacked) {
            $this->actor->click('#stackedSubmit');
        } else {
            $this->actor->click("{$action}する");
        }
        $this->actor->wait(3);
        $this->actor->see("求人原稿情報を{$action}してもよろしいですか？");
        $this->actor->click('OK');
        $this->actor->wait(10);
        $this->actor->seeInTitle('求人原稿情報 - 完了');
        $this->actor->see("{$action}完了", 'h1');
        if ($action == '登録') {
            $this->actor->see("求人原稿情報が{$action}されました", 'p');
        } elseif ($action == '変更') {
            $this->actor->see("求人原稿情報の内容が{$action}されました", 'p');
        }
    }

    /**
     * 入力内容を記憶しておく
     * 一覧や更新画面に登録内容が反映されているか確認するのに使う想定
     * todo まとめられそうならBaseRegisterにまとめる
     * @param $attribute
     * @param $value
     */
    public function fillAndRemember($attribute, $value)
    {
        $this->actor->fillField("#jobmaster-$attribute", $value);
        $this->attributes[$attribute] = $value;
    }

    /**
     * @param object $displayItem instance of JobColumnSet
     * @param string $value
     * @param string $displayPlace 'main' or 'list'
     */
    public function fillEditableTextareaAndRemember($displayItem, $value, $displayPlace)
    {
        $this->actor->click("#{$displayPlace}-{$displayItem->column_name}"); // Editableのinputを表示
        $this->actor->see($displayItem->column_explain, ".editableHint");
        $this->actor->fillField('.editable-input > textarea', $value); // Editableのinputに入力
        $this->actor->pressKey('.editable-input > textarea', [WebDriverKeys::CONTROL, WebDriverKeys::ENTER]); // ctrl + enterで変更をsubmit
        $this->attributes[$displayItem->column_name] = $value;
    }

    /**
     * @param object $displayItem instance of JobColumnSet
     * @param string $value
     * @param string $displayPlace 'main' or 'list'
     */
    public function fillEditableTextAndRemember($displayItem, $value, $displayPlace)
    {
        $this->actor->click("#{$displayPlace}-{$displayItem->column_name}"); // Editableのinputを表示
        $this->actor->see($displayItem->column_explain, ".editableHint");
        $this->actor->fillField('.editable-input > input', $value); // Editableのinputに入力
        $this->actor->click('#jobinfo'); // focusを外して変更をsubmit
        $this->attributes[$displayItem->column_name] = $value;
    }

    /**
     * @param object $displayItem instance of JobColumnSet
     * @param array $values
     * @param string $displayPlace 'main' or 'list'
     */
    public function checkEditableAndRemember($displayItem, $values, $displayPlace)
    {
        $this->actor->click("#{$displayPlace}-{$displayItem->column_name}"); // Editableのinputを表示
        $this->actor->see($displayItem->column_explain, ".editableHint");
        foreach ($values as $value) {
            $id = $this->actor->grabAttributeFrom("//input[@type='checkbox' and @value='{$value}']", 'id');
            $this->actor->click("//label[@for='{$id}']"); // Editableのinputに入力
        }
        $this->actor->click('#jobinfo'); // focusを外して変更をsubmit
        $this->attributes[$displayItem->column_name] = implode(', ', $values);
    }

    /**
     * @param object $displayItem instance of JobColumnSet
     * @param string $value
     * @param string $displayPlace 'main' or 'list'
     */
    public function selectEditableAndRemember($displayItem, $value, $displayPlace)
    {
        $this->actor->click("#{$displayPlace}-{$displayItem->column_name}"); // Editableのinputを表示
        $this->actor->see($displayItem->column_explain, ".editableHint");
        $this->actor->selectOption('.form-control', $value); // Editableのinputに入力
        $this->attributes[$displayItem->column_name] = $value;
    }

    /**
     * @param string $attribute
     * @param bool $required
     */
    public function checkHintInput($attribute, $required = true)
    {
        /** @var JobColumnSet[] $items */
        $items = Yii::$app->functionItemSet->job->items;

            $this->actor->seeInSource($this->toSource($items[$attribute]->explain));
    }

    /**
     * 項目説明文をhtml上に表示される形に置き換える
     * html上には<br />ではなく<br>が、\r\nではなく\nが表示されるため、
     * それぞれ置き換えている（PhantomJsの場合？）
     * @param $text
     * @return mixed
     */
    private function toSource($text)
    {
        $explain = str_replace(['<br />', "\r\n"], ['<br>', "\n"], $text);
        return Html::tag('div', $explain, ['class' => 'hint-block']);
    }

    /**
     * disp_type.disp_type_no=3の代理店、掲載企業、プランの組み合わせを準備
     * @return array
     */
    public static function initPlan()
    {
        $dispTypeId = DispType::findOne(['disp_type_no' => 3]);
        $corp = CorpMaster::findOne(['valid_chk' => 1]);
        $client = ClientMaster::findOne(['corp_master_id' => $corp->id, 'valid_chk' => 1]);
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
        ];
    }
}
