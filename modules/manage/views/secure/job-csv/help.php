<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/03/03
 * Time: 15:25
 */

use app\models\manage\SearchkeyMaster;
use yii\helpers\Html;
use proseeds\helpers\CommonHtml;
use yii\helpers\ArrayHelper;

$this->title = Yii::t('app', "求人情報登録用CSVの入力方法");

echo Html::tag("div", CommonHtml::manageTitle($this->title, '', 'user'), ['class' => 'mgt10']);
echo Html::tag("h2", Yii::t("app", "CSVの項目"));

$header = [Yii::t("app", '項目名'), Yii::t("app", '内容')];
echo CommonHtml::tableView($header, ArrayHelper::merge(
    [[Yii::t('app', '状態(必須)'), Yii::t('app', '求人原稿の有効・無効を入力します。有効の場合は1、無効の場合は0を入力してください。状態は必須項目です。')]],
    ArrayHelper::getColumn(Yii::$app->functionItemSet->job->items, 'description'),
    SearchkeyMaster::csvDescription(),
    [[Yii::t('app', '他サイト連携ID'), Yii::t('app', '外部サイトと関連付けるためのIDです。掲載企業毎にユニークなIDを入力してください。')]]
));

echo Html::tag("h2", Yii::t("app", "求人情報を変更する際の注意事項"));
echo Html::beginTag("ul");
echo Html::tag("li", Yii::t("app", "求人情報を変更する際は、変更する『仕事ID』を入力してください。入力しない場合は、新規登録されます。"));
echo Html::tag("li", Yii::t("app", "空欄にした項目に関しては内容が削除されてしまうので、ご注意ください。"));
echo Html::endTag("ul");

echo Html::tag("h2", Yii::t("app", "CSVの基本仕様"));
echo Html::beginTag("ul");
echo Html::tag("li", Yii::t("app", "フィールド(列)区切り文字は「,(カンマ)」とする"));
echo Html::tag("li", Yii::t("app", "レコード(行)区切り文字は「CR+LF改行（ASCIIコード：13）」とする"));
echo Html::tag("li", Yii::t("app", "CSVファイルは1行目は行見出し(項目名)として読み飛ばすものとする"));
echo Html::tag("li", Yii::t("app", "「,(カンマ)」が入っている場合は、必ずフィールドは「\"(ダブルクォート)」で囲むものとする"));
echo Html::endTag("ul");