<?php

/* @var $scenario Codeception\Scenario */

use app\models\JobMasterDisp;
use app\models\manage\ApplicationColumnSet;
use app\models\manage\searchkey\Pref;
use tests\codeception\_pages\ApplyPage;
use tests\codeception\_pages\manage\application\ApplicationSearchPage;
use tests\codeception\_pages\manage\ManageLoginPage;

// テスト準備 //////////////////////////////////////////////////////////////////////////////////////////////////////////
// fixture読み込み
(new \tests\codeception\fixtures\ApplicationColumnSetFixture())->load();
(new \tests\codeception\fixtures\ApplicationColumnSubsetFixture())->load();

// 掲載中で、メールアドレスも設定されているレコードを取得
/** @var JobMasterDisp $job */
$job = JobMasterDisp::find()->active()->andWhere(['and',
    ['not', ['application_mail' => null]],
    ['not', ['application_mail' => '']],
])->one();

$jobInvalid = JobMasterDisp::find()->active()->andWhere(['application_mail' => ''])->one();

// todo columnSetの状態（の変化）に対応
$numLabel = Yii::$app->functionItemSet->application->items['application_no']->label;

// それぞれの入力文字数上限
$maxSei = 200;
$maxMei = 200;
$maxKanaSei = 200;
$maxKanaMei = 200;

// 必須アイテム数(メールアドレスは必須入力固定でis_mustがnullなので1つ足している)
$numOfRequiredItems = ApplicationColumnSet::find()->where(['is_must' => ApplicationColumnSet::MUST])->count() + 1;

// パンくずのリンク確認用
// 改行が含まれている場合、ブラウザ上では半角スペースとして表示されるため
$corpNameDisp = preg_replace('((\\r\\n)+|\\r+|\\n+)', ' ', $job->corp_name_disp);
$jobSearchNumber = preg_replace('((\\r\\n)+|\\r+|\\n+)', ' ', $job->job_search_number);
//----------------------
// 有効な仕事の応募画面へ遷移
//----------------------
$I = new AcceptanceTester($scenario);

$I->wait(1);
$page = ApplyPage::openBy($I, ['job_no' => $jobInvalid->job_no]);
$I->amGoingTo('有効でもメールアドレスが設定されていない仕事情報の応募画面には遷移できない');
$I->see('残念ですが、お探しのページは見つかりませんでした', 'h1');

$page = ApplyPage::openBy($I, ['job_no' => $job->job_no]);
$I->wait(1);

// テスト開始 //////////////////////////////////////////////////////////////////////////////////////////////////////////
//----------------------
// 正常に遷移できているか
//----------------------
$I->see('応募情報をご入力ください', 'h2');
$I->amGoingTo('各項目の初期状態のチェック');
$page->textInputColour('name_sei', ApplyPage::RED);
$page->textInputColour('name_mei', ApplyPage::RED);
$page->textInputColour('kana_mei', ApplyPage::RED);
$page->textInputColour('kana_mei', ApplyPage::RED);
$page->cellColour('sex', ApplyPage::RED);
$page->selectColour('birthdateyear', ApplyPage::RED, 'birth birthY');
$page->selectColour('birthdatemonth', ApplyPage::RED, 'birth birthM');
$page->selectColour('birthdateday', ApplyPage::RED, 'birth birthD');
$page->selectColour('pref_id', ApplyPage::RED);
$page->textInputColour('address', ApplyPage::WHITE);
$page->textInputColour('tel_no', ApplyPage::RED);
$page->mailInputColour(ApplyPage::RED);
$page->selectColour('occupation_id', ApplyPage::WHITE);
$page->textInputColour('self_pr', ApplyPage::RED);
$page->cellColour('option100', ApplyPage::WHITE);
$page->cellColour('option101', ApplyPage::WHITE);

$I->amGoingTo('パンくずの表示確認');
$I->see("{$corpNameDisp}の求人詳細", '.breadcrumb li');
$I->see("{$corpNameDisp}応募", '.breadcrumb li');

// 応募ページから応募以外で離脱する場合、jsアラートが走るため、パンくずのリンクはチェックしない。
//$I->click("{$corpNameDisp}の求人詳細");
//$I->wait(5);
//$I->see($jobSearchNumber);
//$I->moveBack();
//$I->wait(5);

$I->amGoingTo('全都道府県が選択可能');
$prefNos = Pref::find()->select('id')->column();
foreach ($prefNos as $k => $prefNo) {
    $i = $k + 2;
    $I->seeElementInDOM("//select[@id='apply-pref_id']/option[{$i}][@value='{$prefNo}']");
}

//----------------------
// 必須入力の氏名を使って1つのcellに2つのinputがあるときのエラーテスト
// 名が初期状態のとき
//----------------------
// 入力
$I->amGoingTo('姓：不正な値を入力　名：初期状態');
$I->fillField('#apply-name_sei', str_repeat('a', $maxSei + 1));
$I->wait(1);

// 結果
$I->expect('姓：エラーメッセージ出てinputは赤い　名：エラーメッセージ出ずinputは赤い　ラベル・フォームの枠：赤　残り必須項目数：変化なし');
$I->see("姓は{$maxSei}文字以下で入力してください。");
$page->textInputColour('name_sei', ApplyPage::RED);
$I->cantSee('名は必須入力です。');
$page->textInputColour('name_mei', ApplyPage::RED);
$I->seeElement('.field-apply-fullname.required.has-error');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 入力
$I->amGoingTo('姓：正しい値を入力　名：初期状態');
$I->fillField('#apply-name_sei', str_repeat('a', $maxSei));
$I->wait(1);

// 結果
$I->expect('姓：エラーメッセージ出ずinputは白い　名：エラーメッセージ出ずinputは赤い　ラベル・フォームの枠：赤　残り必須項目数：変化なし');
$I->cantSee('姓は必須入力です。');
$I->cantSee("姓は{$maxSei}文字以下で入力してください。");
$page->textInputColour('name_sei', ApplyPage::WHITE);
$I->cantSee('名は必須入力です。');
$page->textInputColour('name_mei', ApplyPage::RED);
$I->seeElement('.field-apply-fullname.required.has-error');
$I->see($numOfRequiredItems, '.requiredItemNum');

//----------------------
// 必須入力の氏名を使って1つのcellに2つのinputがあるときのエラーテスト
// 名がエラーのとき
//----------------------
// 準備
$I->fillField('#apply-name_mei', str_repeat('b', $maxMei + 1));
$I->wait(1);

// 入力
$I->amGoingTo('姓：不正な値を入力　名：エラー状態');
$I->fillField('#apply-name_sei', '');
$I->wait(1);

// 結果
$I->expect('姓：エラーメッセージ出てinputは赤い　名：エラーメッセージ出てinputは赤い　ラベル・フォームの枠：赤　残り必須項目数：変化なし');
$I->see('姓は必須項目です。');
$page->textInputColour('name_sei', ApplyPage::RED);
$I->see("名は{$maxMei}文字以下で入力してください。");
$page->textInputColour('name_mei', ApplyPage::RED);
$I->seeElement('.field-apply-fullname.required.has-error');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 入力
$I->amGoingTo('姓：正しい値を入力　名：初期状態');
$I->fillField('#apply-name_sei', str_repeat('a', $maxSei));
$I->wait(1);

// 結果
$I->expect('姓：エラーメッセージ出ずinputは白い　名：エラーメッセージ出てinputは赤い　ラベル・フォームの枠：赤　残り必須項目数：変化なし');
$I->cantSee('姓は必須入力です。');
$I->cantSee("姓は{$maxSei}文字以下で入力してください。");
$page->textInputColour('name_sei', ApplyPage::WHITE);
$I->see("名は{$maxMei}文字以下で入力してください。");
$page->textInputColour('name_mei', ApplyPage::RED);
$I->seeElement('.field-apply-fullname.required.has-error');
$I->see($numOfRequiredItems, '.requiredItemNum');

//----------------------
// 必須入力の氏名を使って1つのcellに2つのinputがあるときのエラーテスト
// 名が正常なとき
//----------------------
// 準備
$I->fillField('#apply-name_mei', str_repeat('b', $maxMei));
$I->wait(1);

// 入力
$I->amGoingTo('姓：不正な値を入力　名：正常な状態');
$I->fillField('#apply-name_sei', '');
$I->wait(1);

// 結果
$I->expect('姓：エラーメッセージ出てinputは赤い　名：エラーメッセージ出ずinputは白い　ラベル・フォームの枠：赤　残り必須項目数：変化なし');
$I->see('姓は必須項目です。');
$page->textInputColour('name_sei', ApplyPage::RED);
$I->cantSee('名は必須入力です。');
$I->cantSee("名は{$maxMei}文字以下で入力してください。");
$page->textInputColour('name_mei', ApplyPage::WHITE);
$I->seeElement('.field-apply-fullname.required.has-error');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 入力
$I->amGoingTo('姓：正しい値を入力　名：正常な状態');
$I->fillField('#apply-name_sei', str_repeat('a', $maxSei));
$I->wait(1);

// 結果
$I->expect('姓：エラーメッセージ出ずinputは白い　名：エラーメッセージ出ずinputは白い　ラベル・フォームの枠：緑　残り必須項目数：-1');
$I->cantSee('姓は必須入力です。');
$I->cantSee("姓は{$maxSei}文字以下で入力してください。");
$page->textInputColour('name_sei', ApplyPage::WHITE);
$I->cantSee('名は必須入力です。');
$I->cantSee("名は{$maxMei}文字以下で入力してください。");
$page->textInputColour('name_mei', ApplyPage::WHITE);
$I->seeElement('.field-apply-fullname.required.has-success');
$I->see(--$numOfRequiredItems, '.requiredItemNum');

//----------------------
// 必須入力のフリガナを使って1つのcellに2つのinputがあるときのエラーテスト
// セイが初期状態なとき
//----------------------
// 入力
$I->amGoingTo('セイ：初期状態　メイ：正しい値を入力');
$I->fillField('#apply-kana_mei', str_repeat('d', $maxMei));
$I->wait(1);

// 結果
$I->expect('姓：エラーメッセージ出ずinputは赤い　名：エラーメッセージ出ずinputは白い　ラベル・フォームの枠：赤　残り必須項目数：変化なし');
$I->cantSee('セイは必須項目です。');
$page->textInputColour('kana_sei', ApplyPage::RED);
$I->cantSee('メイは必須入力です。');
$I->cantSee("メイは{$maxKanaMei}文字以下で入力してください。");
$page->textInputColour('kana_mei', ApplyPage::WHITE);
$I->seeElement('.field-apply-fullnamekana.required.has-error');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 入力
$I->amGoingTo('セイ：初期状態　メイ：不正な値を入力');
$I->fillField('#apply-kana_mei', '');
$I->wait(1);

// 結果
$I->expect('姓：エラーメッセージ出ずinputは赤い　名：エラーメッセージ出てinputは赤い　ラベル・フォームの枠：赤　残り必須項目数：変化なし');
$I->cantSee('セイは必須項目です。');
$page->textInputColour('kana_sei', ApplyPage::RED);
$I->see('メイは必須項目です。');
$page->textInputColour('kana_mei', ApplyPage::RED);
$I->seeElement('.field-apply-fullnamekana.required.has-error');
$I->see($numOfRequiredItems, '.requiredItemNum');

$I->amGoingTo('セイ：正常な値を入力　メイ：正常な値を入力');
$I->fillField('#apply-kana_sei', str_repeat('c', $maxMei));
$I->fillField('#apply-kana_mei', str_repeat('d', $maxMei));
$I->wait(1);
$I->expect('残り必須項目数が一つ減る');
$I->see(--$numOfRequiredItems, '.requiredItemNum');

//----------------------
// ラジオボタンの動き
// 必須になっている性別で検証
//----------------------
$I->amGoingTo('【ラジオボタン】性別をチェック');
$I->click('label[for=apply-sex-0]'); // ラジオボタンは実は隠れているので
$I->wait(1);
$page->cellColour('sex', ApplyPage::WHITE);
$I->expect('残り必須項目数が一つ減る');
$I->see(--$numOfRequiredItems, '.requiredItemNum');

//----------------------
// ドロップダウンの動き
// 必須になっている都道府県で検証
//----------------------
$I->amGoingTo('【ドロップダウン】都道府県を選択');
$I->selectOption('#apply-pref_id', '茨城県');
$I->wait(1);
$I->expect('【ドロップダウン】inputが白くなり、残り必須項目数が一つ減る');
$page->selectColour('pref_id', ApplyPage::WHITE);
$I->see(--$numOfRequiredItems, '.requiredItemNum');

$I->amGoingTo('【ドロップダウン】初期選択に戻す');
$I->selectOption('#apply-pref_id', '--選択してください--');
$I->wait(1);
$I->expect('【ドロップダウン】inputが赤くなりエラー文言が出て残り必須項目が一つ増える');
$page->selectColour('pref_id', ApplyPage::RED);
$I->see('都道府県は必須項目です。');
$I->see(++$numOfRequiredItems, '.requiredItemNum');

//----------------------
// 必須になっている誕生日の動き
//----------------------
$I->amGoingTo('【誕生日１】初期状態');
$page->canSelectDay(31);

// 年に閏年を入力(2000/-/-)
$I->amGoingTo('【誕生日２】年に閏年を入力(2000/-/-)');
$I->selectOption('#apply-birthdateyear', '2000');
$I->wait(1);
// 結果
$page->canSelectDay(31);
$page->birthdayInputColour('白', '赤', '赤');
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 大の月を入力(2000/1/-)
$I->amGoingTo('【誕生日３】大の月を入力(2000/1/-)');
$I->selectOption('#apply-birthdatemonth', '01');
$I->wait(1);
// 結果
$page->canSelectDay(31);
$page->birthdayInputColour('白', '白', '赤');
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 日付に31を入力(2000/1/31)
$I->amGoingTo('【誕生日４】日付に31を入力(2000/1/31)');
$I->click('#apply-birthdateday'); // focus時に一瞬全部の選択肢を消しているので事前にクリックして先にfocusしています
$I->selectOption('#apply-birthdateday', '31');
$I->wait(1);
// 結果
$page->birthdayInputColour('白', '白', '白');
$I->expect('残り必須項目数が一つ減る');
$I->see(--$numOfRequiredItems, '.requiredItemNum');

// 月を2月に変更(2000/2/31→2000/2/-)
$I->amGoingTo('【誕生日５】月を2月に変更(2000/2/31→2000/2/-)');
$I->selectOption('#apply-birthdatemonth', '02');
$I->wait(1);
// 結果
$I->expect('日が初期状態に戻る');
$I->seeOptionIsSelected('#apply-birthdateday', '----');
$page->canSelectDay(29);
$page->birthdayInputColour('白', '白', '赤');
$I->expect('残り必須項目数が一つ増える');
$I->see(++$numOfRequiredItems, '.requiredItemNum');

// 日を29日に設定(2000/02/29)
$I->amGoingTo('【誕生日６】日を29日に設定(2000/02/29)');
$I->click('#apply-birthdateday'); // focus時に一瞬全部の選択肢を消しているので事前にクリックして先にfocusしています
$I->selectOption('#apply-birthdateday', '29');
$I->wait(1);
// 結果
$I->expect('残り必須項目数が一つ減る');
$I->see(--$numOfRequiredItems, '.requiredItemNum');

// 年を他の閏年に変更(1996/02/29)
$I->amGoingTo('【誕生日７】年を他の閏年に変更(1996/02/29→1996/02/29)');
$I->selectOption('#apply-birthdateyear', '1996');
$I->wait(1);
// 結果
$I->expect('日はそのまま');
$I->seeOptionIsSelected('#apply-birthdateday', '29');
$page->canSelectDay(29);
$page->birthdayInputColour('白', '白', '白');
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 年を閏年でない年に変更(1999/02/29→1999/2/-)
$I->amGoingTo('【誕生日８】年を閏年でない年に変更(1999/02/29→1999/2/-)');
$I->selectOption('#apply-birthdateyear', '1999');
$I->wait(1);
// 結果
$I->expect('日が初期状態に戻る');
$I->seeOptionIsSelected('#apply-birthdateday', '----');
$page->canSelectDay(28);
$page->birthdayInputColour('白', '白', '赤');
$I->expect('残り必須項目数が一つ増える');
$I->see(++$numOfRequiredItems, '.requiredItemNum');

// 月を小の月に変更(1999/9/-)
$I->amGoingTo('【誕生日９】月を小の月に変更(1999/9/-)');
$I->selectOption('#apply-birthdatemonth', '09');
$I->wait(1);
// 結果
$page->canSelectDay(30);
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 年を空欄に変更(-/9/-)
$I->amGoingTo('【誕生日１０】年を空欄に変更(-/9/-)');
$I->selectOption('#apply-birthdateyear', '----');
// 結果
$page->canSelectDay(30);
$page->birthdayInputColour('赤', '白', '赤');
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 月を1月に変更(-/1/-)
$I->amGoingTo('【誕生日１１】月を1月に変更(-/1/-)');
$I->selectOption('#apply-birthdatemonth', '01');
$I->wait(1);
// 結果
$page->canSelectDay(31);
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 月を2月に変更(-/2/-)
$I->amGoingTo('【誕生日１２】月を2月に変更(-/2/-)');
$I->selectOption('#apply-birthdatemonth', '02');
$I->wait(1);
// 結果
$page->canSelectDay(29);
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 日を28日に設定(-/02/28)
$I->amGoingTo('【誕生日１３】日を28日に設定(-/02/28)');
$I->click('#apply-birthdateday'); // focus時に一瞬全部の選択肢を消しているので事前にクリックして先にfocusしています
$I->selectOption('#apply-birthdateday', '28');
$I->wait(1);
// 結果
$page->birthdayInputColour('赤', '白', '白');
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

// 年を1999年に設定(1999/02/28)
$I->amGoingTo('【誕生日１４】年を1999年に設定(1999/02/28)');
$I->selectOption('#apply-birthdateyear', '1999');
$I->wait(1);
// 結果
$I->expect('残り必須項目数が一つ減る');
$I->see(--$numOfRequiredItems, '.requiredItemNum');

//----------------------
// 必須になっている働き方要望を使って
// チェックボックスの動きを検証
//----------------------
$I->amGoingTo('働き方要望を1つチェック');
$I->click('label[for=apply-option101-0]');
$I->wait(1);
$page->cellColour('option101', ApplyPage::WHITE);
$I->expect('残り必須項目数が一つ減る');
$I->see(--$numOfRequiredItems, '.requiredItemNum');

$I->amGoingTo('働き方要望2つ目チェック');
$I->click('label[for=apply-option101-1]');
$I->wait(1);
$page->cellColour('option101', ApplyPage::WHITE);
$I->expect('残り必須項目数は変わらない');
$I->see($numOfRequiredItems, '.requiredItemNum');

$I->amGoingTo('両方チェック外す');
$I->click('label[for=apply-option101-0]');
$I->click('label[for=apply-option101-1]');
$I->wait(1);
$page->cellColour('option101', ApplyPage::RED);
$I->expect('残り必須項目数が一つ増える');
$I->see(++$numOfRequiredItems, '.requiredItemNum');

//----------------------
// 確認画面へ遷移
//----------------------
$I->amGoingTo('formを埋める');

// 氏名は姓がa×maxLengthで名がb×maxLength
// フリガナはセイがc×maxLengthでメイがd×maxLength
// 性別は男性
// 生年月日は1999/02/28

// 都道府県
$I->selectOption('#apply-pref_id', '茨城県');
$I->wait(1);
// 残り必須項目数が一つ減る
$I->see(--$numOfRequiredItems, '.requiredItemNum');

// todo 住所

// 電話番号
$I->fillField('#apply-tel_no', '111-2222-3333');
$I->wait(1);
// 残り必須項目数が一つ減る
$I->see(--$numOfRequiredItems, '.requiredItemNum');

// メールアドレス
$I->fillField('#apply-mail_address', 'sonzaishinai@pro-seeds.co.jp');
$I->wait(1);
// 残り必須項目数が一つ減る
$I->see(--$numOfRequiredItems, '.requiredItemNum');

// todo 現在の職業

// PR
$I->fillField('#apply-self_pr', '猛烈な自己PR');
$I->wait(1);
// 残り必須項目数が一つ減る
$I->see(--$numOfRequiredItems, '.requiredItemNum');

// option101（働き方要望）
$I->click('label[for=apply-option101-0]');
$checkbox1 = $I->grabTextFrom('label[for=apply-option101-0]');
$I->click('label[for=apply-option101-1]');
$checkbox2 = $I->grabTextFrom('label[for=apply-option101-1]');
$I->wait(1);
// 残り必須項目数が一つ減る
$I->see(--$numOfRequiredItems, '.requiredItemNum');

// option108（学力テスト）(数字)
$I->fillField('#apply-option108', '1');
$I->wait(1);
// 残り必須項目数が一つ減る
$I->see(--$numOfRequiredItems, '.requiredItemNum');

// todo その他option

$I->amGoingTo('確認画面へ遷移して内容を検証');
$page->apply();

$I->see('以下の内容でお間違えなければ「応募する」ボタンを押してください。');
$I->see(str_repeat('a', $maxSei) . ' ' . str_repeat('b', $maxMei));
$I->see(str_repeat('c', $maxKanaSei) . ' ' . str_repeat('d', $maxKanaMei));
$I->see('男性');
$I->see('1999年02月28日');// 応募確認の生年月日表記は「YYYY年MM月DD日」に表記変更
$I->see('茨城県');
$I->see('111-2222-3333');
$I->see('sonzaishinai@pro-seeds.co.jp');
$I->see('猛烈な自己PR');
$I->see($checkbox1 . ', ' . $checkbox2);

$I->amGoingTo('ブラウザバック');
$I->moveBack();
$I->wait(1);

$I->expect('誕生日の状態が正常');
$I->seeOptionIsSelected('#apply-birthdateyear', '1999');
$I->seeOptionIsSelected('#apply-birthdatemonth', '02');
$I->seeOptionIsSelected('#apply-birthdateday', '28');
$page->canSelectDay(28);

$I->expect('inputが全部白');
$page->textInputColour('name_sei', ApplyPage::WHITE);
$page->textInputColour('name_mei', ApplyPage::WHITE);
$page->textInputColour('kana_mei', ApplyPage::WHITE);
$page->textInputColour('kana_mei', ApplyPage::WHITE);
$page->cellColour('sex', ApplyPage::WHITE);
$page->selectColour('birthdateyear', ApplyPage::WHITE, 'birth birthY');
$page->selectColour('birthdatemonth', ApplyPage::WHITE, 'birth birthM');
$page->selectColour('birthdateday', ApplyPage::WHITE, 'birth birthD');
$page->selectColour('pref_id', ApplyPage::WHITE);
$page->textInputColour('address', ApplyPage::WHITE);
$page->textInputColour('tel_no', ApplyPage::WHITE);
$page->mailInputColour(ApplyPage::WHITE);
$page->selectColour('occupation_id', ApplyPage::WHITE);
$page->textInputColour('self_pr', ApplyPage::WHITE);
$page->cellColour('option100', ApplyPage::WHITE);
$page->cellColour('option101', ApplyPage::WHITE);

$I->expect('エラーが出ているものが一つもない');
$I->cantSeeElement('.has-error');

$I->amGoingTo('確認画面へ遷移してパンくずの表示確認');
$page->apply();
$I->see("{$corpNameDisp}の求人詳細", '.breadcrumb li');
$I->see("{$corpNameDisp}応募確認", '.breadcrumb li');

$I->amGoingTo('パンくずから求人詳細ページへ移動');
$I->click("{$corpNameDisp}の求人詳細");
$I->wait(5);
$I->see($jobSearchNumber);
$I->moveBack();
$I->wait(5);

$I->amGoingTo('戻るボタン');
$I->click('戻る');
$I->wait(1);

$I->expect('誕生日の状態が正常');
$I->seeOptionIsSelected('#apply-birthdateyear', '1999');
$I->seeOptionIsSelected('#apply-birthdatemonth', '02');
$I->seeOptionIsSelected('#apply-birthdateday', '28');
$page->canSelectDay(28);

$I->expect('inputが全部白');
$page->textInputColour('name_sei', ApplyPage::WHITE);
$page->textInputColour('name_mei', ApplyPage::WHITE);
$page->textInputColour('kana_mei', ApplyPage::WHITE);
$page->textInputColour('kana_mei', ApplyPage::WHITE);
$page->cellColour('sex', ApplyPage::WHITE);
$page->selectColour('birthdateyear', ApplyPage::WHITE, 'birth birthY');
$page->selectColour('birthdatemonth', ApplyPage::WHITE, 'birth birthM');
$page->selectColour('birthdateday', ApplyPage::WHITE, 'birth birthD');
$page->selectColour('pref_id', ApplyPage::WHITE);
$page->textInputColour('address', ApplyPage::WHITE);
$page->textInputColour('tel_no', ApplyPage::WHITE);
$page->mailInputColour(ApplyPage::WHITE);
$page->selectColour('occupation_id', ApplyPage::WHITE);
$page->textInputColour('self_pr', ApplyPage::WHITE);
$page->cellColour('option100', ApplyPage::WHITE);
$page->cellColour('option101', ApplyPage::WHITE);

$I->expect('エラーが出ているものが一つもない');
$I->cantSeeElement('.has-error');

$I->amGoingTo('応募する');
$page->apply();
$I->click('応募する');
$I->wait(3);

$I->expect('応募できる');
$I->see('ご応募ありがとうございました。');
$applicationNo = str_replace("{$numLabel}：", '', $I->grabTextFrom('h4'));

//----------------------
// ナンバーinc検証
//----------------------
// もう一件登録
$I->amGoingTo('もう一件登録');
$I->moveBack();
$I->click('応募する');
$I->wait(3);
$I->see('ご応募ありがとうございました。');
$applicationNo++;
$I->see("{$numLabel}：{$applicationNo}", 'h4');

// 別セッションで1件削除
$I->amGoingTo('先ほど登録したものを削除');
$admin = $I->haveFriend('admin');

$admin->does(function (AcceptanceTester $I) {
    $I->resizeWindow(1200, 800);
    $loginPage = ManageLoginPage::openBy($I);
    $I->amGoingTo('運営元でアクセス');
    $loginPage->login('admin01', 'admin01');
    $I->wait(1);
    $page = ApplicationSearchPage::openBy($I);
    $I->wait(2);

    $I->wantTo('一覧を表示');
    $I->click('この条件で表示する');
    $I->wait(5);

    $I->wantTo('一番上だけチェック');
    $page->clickCheckbox(0);
    $page->clickCheckbox(1);

    $I->wantTo('削除する');
    $I->click('まとめて削除する');
    $I->wait(1);
    $I->click('OK');
    $I->wait(2);
    $I->see('1件のデータが削除されました。', 'pre');
});

// さらに一件登録
$I->amGoingTo('さらに一件登録');
$I->moveBack();
$I->click('応募する');
$I->wait(3);
$I->see('ご応募ありがとうございました。');
$applicationNo++;
$I->see("{$numLabel}：{$applicationNo}", 'h4');

// todo next:電話番号の動き、メールアドレスの動き、エラーがあると確認画面いけない、ブラウザバックしようとした時の動き、db内の確認
