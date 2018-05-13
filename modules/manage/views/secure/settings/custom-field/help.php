<?php
use yii\helpers\Html;
use app\models\manage\CustomField;

$this->title = Yii::t('app', 'カスタムフィールドCSVの入力方法');
$h1 = Html::beginTag('h1', ['class' => 'heading']);
$h1 .= Html::beginTag('span', ['class' => 'glyphicon glyphicon-user']);
$h1 .= Html::endTag('span');
$h1 .= Html::encode($this->title);
$h1 .= Html::endTag('h1');
echo Html::tag('div', $h1, ['class' => 'mgt10']);

echo Html::tag('h2', Yii::t('app', 'CSVの項目'));

$header = [['text' => Yii::t('app', '列名'), 'options' => ['style' => 'width: 25%;']] , Yii::t('app', '内容')];

$labels = (new CustomField())->attributeLabels();
$contents = [
    'custom_no' => [$labels['custom_no'], Yii::t('app', '各カスタムフィールドに割り当てられたNoになります。')],
    'detail' => [$labels['detail'], Yii::t('app', '表示させたい内容を入力してください。').'<br />'.Yii::t('app', '※最大500文字までになります。')],
    'url' => [$labels['url'], Yii::t('app', 'カスタムフィールドを設定したいURLを入力してください。').'<br />'.Yii::t('app', '※最大2000文字までになります。')],
    'pict' => [$labels['pict'], Yii::t('app', '該当ページで表示させたい画像のパスを入力してください。').'<br />'.Yii::t('app', '※CSV登録で画像のアップロードは出来ません。既にアップロードしている画像のパスを入力してください。').'<br />'.Yii::t('app', '※最大255文字までになります。')],
    'valid_chk' => [$labels['valid_chk'], Yii::t('app', '公開の場合は1、非公開の場合は0を入力してください。')],
];

echo Html::beginTag('table', ['class' => 'table table-striped table-bordered detail-view']);

echo Html::beginTag('tr');
foreach ((array)$header as $value) {
    if (is_array($value)) {
        echo Html::tag('th', $value['text'], $value['options']);
    } else {
        echo Html::tag('th', $value);
    }
}
echo Html::endTag('tr');

foreach ($contents as $value) {
    echo Html::beginTag('tr');
    echo Html::tag('td', $value[0]);
    echo Html::tag('td', $value[1]);
    echo Html::endTag('tr');
}
echo Html::endTag('table');

echo Html::tag('h2', Yii::t('app', 'カスタムフィールドを変更する際の注意事項'));
echo Html::beginTag('ul');
echo Html::tag('li', Yii::t('app', 'カスタムフィールドを変更する際は、変更する『No.』を入力してください。入力しない場合は、新規登録されます。'));
echo Html::tag('li', Yii::t('app', '変更の場合、空欄にした項目に関しては内容が削除されてしまうので、ご注意ください。'));
echo Html::endTag('ul');

echo Html::tag('h2', Yii::t('app', 'CSVの基本仕様'));
echo Html::beginTag('ul');
echo Html::tag('li', Yii::t('app', 'フィールド(列)区切り文字は「,(カンマ)」とします。'));
echo Html::tag('li', Yii::t('app', 'レコード(行)区切り文字は「CR+LF改行（ASCIIコード：13）」とします。'));
echo Html::tag('li', Yii::t('app', 'CSVファイルは1行目は行見出し(項目名)として読み飛ばすものとします。'));
echo Html::tag('li', Yii::t('app', '「,(カンマ)」が入っている場合は、必ずフィールドは「\'(ダブルクォート)」で囲むものとします。'));
echo Html::endTag('ul');
