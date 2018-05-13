<?php
/**
 * Created by PhpStorm.
 * User: KNakamoto
 * Date: 2016/03/03
 * Time: 15:25
 */

use yii\helpers\Html;
use app\models\ToolMaster;
use app\common\Site;
use app\common\ColumnSet;

$this->title = Yii::t('app', 'TDK管理CSVの入力方法');

$h1 = Html::beginTag('h1', ['class' => 'heading']);
$h1 .= Html::beginTag('span', ['class' => 'glyphicon glyphicon-user']);
$h1 .= Html::endTag('span');
$h1 .= Html::encode($this->title);
$h1 .= Html::endTag('h1');
echo Html::tag('div', $h1, ['class' => 'mgt10']);

echo Html::tag('h2', Yii::t('app', 'CSVの項目'));

$header = [['text' => Yii::t('app', '列名'), 'options' => ['style' => 'width: 25%;']] , Yii::t('app', '内容')];

$labels = (new ToolMaster())->attributeLabels();
$contents = [
    'tool_no' => [$labels['tool_no'], Yii::t('app', '各ページに割り当てられたNoになります。').'<br />'.Yii::t('app', '※編集すると、エラーになりますので、編集しないでください。')],
    'page_name' => [$labels['page_name'], Yii::t('app', '各ページのページ名になります。').'<br />'.Yii::t('app', '※編集すると、エラーになりますので、編集しないでください。')],
    'title' => [$labels['title'], Yii::t('app', '該当ページで表示させたいタイトルを入力してください。').'<br />'.Yii::t('app', '※最大50文字までになります。')],
    'description' => [$labels['description'], Yii::t('app', '該当ページで表示させたいディスクリプションを入力してください。').'<br />'.Yii::t('app', '※最大200文字までになります。')],
    'keywords' => [$labels['keywords'], Yii::t('app', '該当ページで表示させたいキーワードを入力してください。').'<br />'.Yii::t('app', '※最大200文字までになります。')],
    'h1' => [$labels['h1'], Yii::t('app', '該当ページで表示させたいh1を入力してください。').'<br />'.Yii::t('app', '※最大100文字までになります。')],
];

echo Html::beginTag("table", ['class' => 'table table-striped table-bordered detail-view']);

echo Html::beginTag('tr');
foreach((array)$header as $value){
    if (is_array($value)) {
        echo Html::tag('th', $value['text'], $value['options']);
    } else {
        echo Html::tag('th', $value);
    }
}
echo Html::endTag('tr');

foreach($contents as $value) {
    echo Html::beginTag('tr');
    echo Html::tag('td', $value[0]);
    echo Html::tag('td', $value[1]);
    echo Html::endTag('tr');
}

echo Html::endTag('table');


echo Html::tag('h2', Yii::t('app', '動的変数一覧'));

$varsHeader = [
    ['text' => Yii::t('app', '利用可能なページ名'), 'options' => ['style' => 'width: 25%;']],
    ['text' => Yii::t('app', '変数名'), 'options' => ['style' => 'width: 20%;']],
    Yii::t('app', '内容')];

echo Html::beginTag('table', ['class' => 'table table-bordered detail-view']);

echo Html::beginTag('tr');
foreach((array)$varsHeader as $value){
    if (is_array($value)) {
        echo Html::tag('th', $value['text'], $value['options']);
    } else {
        echo Html::tag('th', $value);
    }
}
echo Html::endTag('tr');

echo Html::beginTag('tr');
echo Html::tag('td', Yii::t('app', '全ページ'), ['rowspan' => 2]);
echo Html::tag('td', '[SITENAME]');
echo Html::tag('td', Yii::t('app', 'サイト名が表示されます。'));
echo Html::endTag('tr');

echo Html::beginTag('tr');
echo Html::tag('td', '[AREANAME]');
echo Html::tag('td', Yii::t('app', 'ユーザーが選択したエリア名が表示されます。'));
echo Html::endTag('tr');

echo Html::beginTag('tr');
echo Html::tag('td', Yii::t('app', '検索結果ページ'));
echo Html::tag('td', '[SEARCHNAME]');
echo Html::tag('td', Yii::t('app', 'ユーザーが検索した検索条件が表示されます。'));
echo Html::endTag('tr');

$varsPageNmae = Yii::t('app', '原稿詳細ページ<br>応募入力ページ<br>応募確認ページ<br>応募完了ページ<br>携帯に送るページ<br>携帯に送る完了ページ');
$varsLabels = Yii::$app->functionItemSet->job->getTagLabels();
foreach($varsLabels as $jobColumn) {
    echo Html::beginTag('tr');
    if ($jobColumn->column_name == 'job_no') {
        echo Html::tag('td', $varsPageNmae, ['rowspan' => count(Site::TAG_CONVERSION_MAP)]);
    }
    echo Html::tag('td', Yii::t('app', array_search($jobColumn->column_name, Site::TAG_CONVERSION_MAP)));
    if ($jobColumn->column_name == 'job_no') {
        echo Html::tag('td', Yii::t('app', '自動で発行された{label}が表示されます。', ['label' => $jobColumn->label]));
    }else{
        echo Html::tag('td', Yii::t('app', '{label}で登録した内容が表示されます。', ['label' => $jobColumn->label]));
    }
    echo Html::endTag('tr');
}

echo Html::endTag('table');


echo Html::tag('h2', Yii::t('app', 'タグ情報を変更する際の注意事項'));
echo Html::beginTag('ul');
echo Html::tag('li', Yii::t('app', '変数は大文字、小文字を区別します。'));
echo Html::tag('li', Yii::t('app', '上記以外の変数は利用できません。変数は一つのタグ内で複数指定する事も可能です。'));
echo Html::tag('li', Yii::t('app', 'CSVファイルの行と列の削除は行わないでください。'));
echo Html::endTag('ul');

echo Html::tag('h2', Yii::t('app', 'CSVの基本仕様'));
echo Html::beginTag('ul');
echo Html::tag('li', Yii::t('app', 'フィールド(列)区切り文字は「,(カンマ)」とします。'));
echo Html::tag('li', Yii::t('app', 'レコード(行)区切り文字は「CR+LF改行（ASCIIコード：13）」とします。'));
echo Html::tag('li', Yii::t('app', 'CSVファイルは1行目は行見出し(項目名)として読み飛ばすものとします。'));
echo Html::tag('li', Yii::t('app', '「,(カンマ)」が入っている場合は、必ずフィールドは「\'(ダブルクォート)」で囲むものとします。'));
echo Html::endTag('ul');
