<?php

namespace tests\codeception\_pages;

use yii\codeception\BasePage;
use yii\helpers\Inflector;

/**
 * Represents about page
 * @property \AcceptanceTester|\FunctionalTester $actor
 */
class ApplyPage extends BasePage
{
    const RED = 0;
    const WHITE = 1;

    public $route = 'apply/index';

    /**
     * validationに失敗してtextのinputの色が赤か白かをチェック
     * @param $attribute
     * @param $colour
     */
    public function textInputColour($attribute, $colour)
    {
        $class = 'form-control input-txt input-txt-large';
        if ($colour === self::RED) {
            $class .= ' form-requiredItem';
        }
        $this->actor->seeElement("#apply-$attribute", ['class' => $class]);
    }

    /**
     * メールのinputの色が赤か白かをチェック
     * メールだけclassが特殊なので別メソッドにしている
     * @param $colour
     */
    public function mailInputColour($colour)
    {
        $required = '';
        if ($colour === self::RED) {
            $required = ' form-requiredItem';
        }
        $this->actor->seeElement('#apply-mail_address', ['class' => "form-control input-txt input-txt-large auto-complete-mail-address$required ui-autocomplete-input"]);
    }

    /**
     * validationに失敗してselectのinputの色が赤か白かをチェック
     * @param $attribute
     * @param $colour
     * @param string $additionalClass
     */
    public function selectColour($attribute, $colour, $additionalClass = 'input-txt')
    {
        $class = 'form-control';
        if ($additionalClass !== '') {
            $class .= ' ' . $additionalClass;
        }
        if ($colour === self::RED) {
            $class .= ' form-requiredItem';
        }
        $this->actor->seeElement("#apply-$attribute", ['class' => $class]);
    }

    /**
     * checkboxやradioのcellの色が赤か白かをチェック
     * @param $attribute
     * @param $colour
     */
    public function cellColour($attribute, $colour)
    {
        $class = '';
        if ($colour === self::RED) {
            $class .= '.form-requiredItem';
        }
        $this->actor->seeElement("#apply-form-$attribute-tr > td$class");
    }

    /**
     * 誕生日の日付selectで何日まで選択できるか
     * @param $day 28|29|30|31
     */
    public function canSelectDay($day)
    {
        switch ($day) {
            case 28:
                $this->actor->expect('28日まで選択可');
                $this->actor->click('#apply-birthdateday');
                $this->actor->wait(1);
                $this->actor->seeElementInDOM('#apply-birthdateday > option[value="28"]');
                $this->actor->cantSeeElementInDOM('#apply-birthdateday > option[value="29"]');
                $this->actor->cantSeeElementInDOM('#apply-birthdateday > option[value="30"]');
                $this->actor->cantSeeElementInDOM('#apply-birthdateday > option[value="31"]');
                break;
            case 29:
                $this->actor->expect('29日まで選択可');
                $this->actor->click('#apply-birthdateday');
                $this->actor->wait(1);
                $this->actor->seeElementInDOM('#apply-birthdateday > option[value="29"]');
                $this->actor->cantSeeElementInDOM('#apply-birthdateday > option[value="30"]');
                $this->actor->cantSeeElementInDOM('#apply-birthdateday > option[value="31"]');
                break;
            case 30:
                $this->actor->expect('30日まで選択可');
                $this->actor->click('#apply-birthdateday');
                $this->actor->wait(1);
                $this->actor->seeElementInDOM('#apply-birthdateday > option[value="29"]');
                $this->actor->seeElementInDOM('#apply-birthdateday > option[value="30"]');
                $this->actor->cantSeeElementInDOM('#apply-birthdateday > option[value="31"]');
                break;
            case 31:
                $this->actor->expect('31日まで選択可');
                $this->actor->click('#apply-birthdateday');
                $this->actor->wait(1);
                $this->actor->seeElementInDOM('#apply-birthdateday > option[value="29"]');
                $this->actor->seeElementInDOM('#apply-birthdateday > option[value="30"]');
                $this->actor->seeElementInDOM('#apply-birthdateday > option[value="31"]');
                break;
            default:
                break;
        }
    }

    /**
     * Ceptでの見やすさを優先してこのようにしています
     * @param $year string 赤か白
     * @param $month string 赤か白
     * @param $day string 赤か白
     */
    public function birthdayInputColour($year, $month, $day)
    {
        $this->actor->expect("inputの色　年：{$year}　月：{$month}　日：{$day}");

        if ($year == '赤') {
            $this->selectColour('birthdateyear', ApplyPage::RED, 'birth birthY');
        } elseif ($year == '白') {
            $this->selectColour('birthdateyear', ApplyPage::WHITE, 'birth birthY');
        }

        if ($month == '赤') {
            $this->selectColour('birthdatemonth', ApplyPage::RED, 'birth birthM');
        } elseif ($month == '白') {
            $this->selectColour('birthdatemonth', ApplyPage::WHITE, 'birth birthM');
        }

        if ($day == '赤') {
            $this->selectColour('birthdateday', ApplyPage::RED, 'birth birthD');
        } elseif ($day == '白') {
            $this->selectColour('birthdateday', ApplyPage::WHITE, 'birth birthD');
        }

        if ($year == '白' && $month == '白' && $day == '白') {
            $this->actor->expect('エラー文無く、ラベルやフォームの枠は緑');
            $this->actor->cantSee('生年月日は必須項目です。');
            $this->actor->seeElement('.field-apply-birth_date.required.has-success');
        } else {
            $this->actor->expect('エラー文が見え、ラベルやフォームの枠は赤');
            $this->actor->see('生年月日は必須項目です。');
            $this->actor->seeElement('.field-apply-birth_date.required.has-error');
        }
    }

    /**
     * 応募画面から確認画面へ遷移する
     */
    public function apply()
    {
        $this->actor->click('同意のうえ応募する');
        $this->actor->wait(2);
    }
}
