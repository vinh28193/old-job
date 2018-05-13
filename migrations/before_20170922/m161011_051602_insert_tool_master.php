<?php

use yii\db\Migration;
use yii\db\Query;

class m161011_051602_insert_tool_master extends Migration
{
    private $records = [
        [
            'tool_no' => 1,
            'page_name' => 'TOP',
            'title' => '[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。',
            'keywords' => '[SITENAME],求人',
            'h1' => '[SITENAME]',
        ],
        [
            'tool_no' => 2,
            'page_name' => 'エリアトップ',
            'title' => '[AREANAME]｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。[AREANAME]版',
            'keywords' => '[SITENAME],[AREANAME],求人',
            'h1' => '[SITENAME]',
        ],
        [
            'tool_no' => 3,
            'page_name' => '検索結果ページ（条件無し）',
            'title' => '求人検索結果｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。検索結果ページ。',
            'keywords' => '[SITENAME],求人,検索結果',
            'h1' => '求人検索結果',
        ],
        [
            'tool_no' => 4,
            'page_name' => '検索結果ページ（検索条件が1つ）',
            'title' => '[SEARCHNAME]の求人検索結果｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。[SEARCHNAME]の検索結果ページ。',
            'keywords' => '[SITENAME],求人,[SEARCHNAME]',
            'h1' => '[SEARCHNAME]の求人検索結果',
        ],
        [
            'tool_no' => 5,
            'page_name' => '検索結果ページ（検索条件が2つ）',
            'title' => '[SEARCHNAME]の求人検索結果｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。[SEARCHNAME]の検索結果ページ。',
            'keywords' => '[SITENAME],求人,[SEARCHNAME]',
            'h1' => '[SEARCHNAME]の求人検索結果',
        ],
        [
            'tool_no' => 6,
            'page_name' => '検索結果ページ（上記以外）',
            'title' => '求人検索結果｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。検索結果ページ。',
            'keywords' => '[SITENAME],求人,検索結果',
            'h1' => '求人検索結果',
        ],
        [
            'tool_no' => 7,
            'page_name' => '原稿詳細ページ',
            'title' => '[NO5]（ID：[NO1]）｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。[NO5]（ID：[NO1]）ページ。',
            'keywords' => '[SITENAME],求人,[NO5]',
            'h1' => '[NO5]（ID：[NO1]）｜[SITENAME]',
        ],
        [
            'tool_no' => 8,
            'page_name' => '応募入力ページ',
            'title' => '[NO5]（ID：[NO1]）に応募する｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。[NO5]（ID：[NO1]）の応募するページ。',
            'keywords' => '[SITENAME],求人,[NO5],応募',
            'h1' => '[NO5]（ID：[NO1]）に応募する｜[SITENAME]',
        ],
        [
            'tool_no' => 9,
            'page_name' => '応募確認ページ',
            'title' => '[NO5]（ID：[NO1]）に応募確認｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。[NO5]（ID：[NO1]）の応募確認ページ。',
            'keywords' => '[SITENAME],求人,[NO5],応募確認',
            'h1' => '[NO5]（ID：[NO1]）に応募確認｜[SITENAME]',
        ],
        [
            'tool_no' => 10,
            'page_name' => '応募完了ページ',
            'title' => '[NO5]（ID：[NO1]）に応募完了｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。[NO5]（ID：[NO1]）の応募完了ページ。',
            'keywords' => '[SITENAME],求人,[NO5],応募確認完了',
            'h1' => '[NO5]（ID：[NO1]）に応募完了｜[SITENAME]',
        ],
        [
            'tool_no' => 11,
            'page_name' => '携帯に送るページ',
            'title' => '携帯に送信（ID：[NO1]）｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。携帯に送信（ID：[NO1]）。',
            'keywords' => '[SITENAME],求人,[NO5],携帯',
            'h1' => '携帯に送信（ID：[NO1]）｜[SITENAME]',
        ],
        [
            'tool_no' => 12,
            'page_name' => '携帯に送る完了ページ',
            'title' => '携帯に送信完了（ID：[NO1]）｜[SITENAME]',
            'description' => 'JobMakerデモサイトのディスクリプションです。携帯に送信完了（ID：[NO1]）。',
            'keywords' => '[SITENAME],求人,[NO5],携帯,送信',
            'h1' => '携帯に送信完了（ID：[NO1]）｜[SITENAME]',
        ],
    ];

    public function safeUp()
    {
        $this->execute('TRUNCATE tool_master');

        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            foreach ($this->records as $row) {
                $this->insert('tool_master', [
                    'tenant_id' => $tenant['tenant_id'],
                    'tool_no' => $row['tool_no'],
                    'page_name' => $row['page_name'],
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'keywords' => $row['keywords'],
                    'h1' => $row['h1'],
                ]);
            }
        }
    }

    public function safeDown()
    {
        $this->execute('TRUNCATE tool_master');
    }
}
