<?php

use app\models\FreeContent;
use proseeds\models\Tenant;
use yii\db\Migration;

/**
 * Class m180313_003452_insert_free_content_sample
 */
class m180313_003452_insert_free_content_sample extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        /** @var Tenant[] $tenants */
        $tenants = Tenant::find()->all();
        foreach ($tenants as $tenant) {
            $this->insert('free_content', [
                'tenant_id' => $tenant->tenant_id,
                'title' => 'パターンライブラリ',
                'keyword' => 'キーワード,キーワード,キーワード',
                'description' => 'ここにはこのページの説明文を記載します。descriptionの文字数は最大長くとも120文字程度におさえましょう。',
                'url_directory' => 'TEMPLATE',
                'valid_chk' => 0,
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            $contentId = FreeContent::find()->select('id')->where([
                'url_directory' => 'TEMPLATE',
                'tenant_id' => $tenant->tenant_id,
            ])->scalar();
            $elemData = [
                [
                    'type' => 1,
                    'image_file_name' => '',
                    'text' => '<div id="freeContents">
<p style="margin:30px 0 15px;border-bottom:1px solid #ccc">▼見出し</p>
<!--見出しを記述する場合はこちら///////////////////////////////////////////////////////////-->
<h1>見出し１</h1>
<h2>見出し２</h2>
<h3>見出し３</h3>
<h4>見出し４</h4>
<h5>見出し５</h5>
<h6>見出し６</h6>

<p style="margin:30px 0 15px;border-bottom:1px solid #ccc">▼文章</p>
<!--文章を記述する場合はこちら///////////////////////////////////////////////////////////-->
<p>
通常のテキストはpタグの中に記載します。改行をするときはbrタグを記述すると改行されます。<br>
<a href="#">テキストリンク</a>はaタグの中に記載し、aタグにはhref属性を使ってURLを指定します。<br>
文章の中で<strong>強調表示</strong>したい部分はstrongタグの中に記載します。<br>
</p>

<p style="margin:30px 0 15px;border-bottom:1px solid #ccc">▼表組み（縦並びの場合）</p>
<!--表組み（縦並びの場合）を記述する場合はこちら///////////////////////////////////////////////////////////-->
<table class="table mod-table1">
    <tbody><tr>
      <th>項目</th>
      <td>データ</td>
    </tr>
    <tr>
      <th>項目</th>
      <td>データ</td>
    </tr>
    <tr>
      <th>項目</th>
      <td>データ</td>
    </tr>
	</tbody>
</table>

<p style="margin:30px 0 15px;border-bottom:1px solid #ccc">▼表組み（横並びの場合）</p>
<!--表組み（横並びの場合）を記述する場合はこちら///////////////////////////////////////////////////////////-->
<table class="table mod-table1">
    <tbody><tr>
      <th>項目</th>
      <th>項目</th>
      <th>項目</th>
    </tr>
    <tr>
      <td>データ</td>
      <td>データ</td>
      <td>データ</td>
    </tr>
    <tr>
      <td>データ</td>
      <td>データ</td>
      <td>データ</td>
    </tr>
	</tbody>
</table>

</div>',
                    'sort' => 1,
                ],
                [
                    'type' => 2,
                    'image_file_name' => 'notExists.png',
                    'text' => '',
                    'sort' => 2,
                ],
                [
                    'type' => 3,
                    'image_file_name' => 'notExists.png',
                    'text' => '<h3>見出し３</h3>
<p>
通常のテキストはpタグの中に記載します。改行をするときはbrタグを記述すると改行されます。<br>
<a href="#">テキストリンク</a>はaタグの中に記載し、aタグにはhref属性を使ってURLを指定します。<br>
文章の中で<strong>強調表示</strong>したい部分はstrongタグの中に記載します。<br>
</p>',
                    'sort' => 3,
                ],
                [
                    'type' => 4,
                    'image_file_name' => 'notExists.png',
                    'text' => '<h3>見出し３</h3>
<p>
通常のテキストはpタグの中に記載します。改行をするときはbrタグを記述すると改行されます。<br>
<a href="#">テキストリンク</a>はaタグの中に記載し、aタグにはhref属性を使ってURLを指定します。<br>
文章の中で<strong>強調表示</strong>したい部分はstrongタグの中に記載します。<br>
</p>',
                    'sort' => 4,
                ],
            ];

            foreach ($elemData as $data) {
                $this->insert('free_content_element', array_merge([
                    'tenant_id' => $tenant->tenant_id,
                    'free_content_id' => $contentId,
                    'created_at' => time(),
                    'updated_at' => time(),
                ], $data));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $contentIds = FreeContent::find()->select('id')->where(['url_directory' => 'TEMPLATE'])->column();
        $this->delete('free_content', ['id' => $contentIds]);
        $this->delete('free_content_element', ['free_content_id' => $contentIds]);
    }
}
