<?php
namespace models\manage;

use app\commands\components\Uploader;
use tests\codeception\fixtures\CustomFieldFixture;
use tests\codeception\unit\JmTestCase;
use app\models\manage\CustomField;
use app\common\Helper\Html;

/**
 * Class CustomFieldTest
 * @package models\manage
 *
 * @property CustomFieldFixture $custom_field
 */
class CustomFieldTest extends JmTestCase
{
    /**
     * テーブル名テスト
     */
    public function testTableName()
    {
        $model = new CustomField();
        verify($model->tableName())->equals('custom_field');
    }

    // initは非常に単純なメソッドなので省略

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new CustomField();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new CustomField();
            $model->validate();
            // custom_noは beforeValidateで必ず入力されるので検証不能
            verify($model->hasErrors('detail'))->true();
            verify($model->hasErrors('url'))->true();
            verify($model->hasErrors('pict'))->false();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new CustomField();
            $model->load([
                $model->formName() => [
                    'tenant_id' => '文字列',
                    'valid_chk' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            // custom_noは beforeValidateで必ず入力されるので検証不能
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('文字列チェック', function () {
            $model = new CustomField();
            $model->load([
                $model->formName() => [
                    'detail' => (int)1,
                    'url' => (int)1,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('detail'))->true();
            verify($model->hasErrors('url'))->true();
        });
        $this->specify('文字列の最大', function () {
            $model = new CustomField();
            $model->load([
                $model->formName() => [
                    'detail' => str_repeat('a', 501),
                    'url' => str_repeat('a', 2001),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('detail'))->true();
            verify($model->hasErrors('url'))->true();
        });
        $this->specify('urlパターンマッチ', function () {
            $model = new CustomField();
            $model->load([
                $model->formName() => [
                    'url' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('url'))->true();
        });
        $this->specify('重複チェックurl', function () {
            // 既存データを取得
            /** @var CustomField $model */
            $model = (new CustomField())->find()->one();
            // 検証用のデータを用意
            $attributes = ['detail' => 'URLユニークチェック', 'url' => $model->url, 'valid_chk' => CustomField::INVALID];
            // 新規データを登録
            $model->isNewRecord = true;
            $model->load([$model->formName() => $attributes]);
            $model->validate();
            verify($model->hasErrors('url'))->true();
        });
        $this->specify('重複チェックcustmo_no', function () {
            // 既存データを2つ取得
            /** @var CustomField $modelA */
            $modelA = (new CustomField())->find()->offset(1)->one();
            /** @var CustomField $modelB */
            $modelB = (new CustomField())->find()->offset(2)->one();
            // modelAのcustom_noをmodelBに上書き
            $modelB->custom_no = $modelA->custom_no;
            $modelB->validate();
            verify($modelB->hasErrors('custom_no'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new CustomField();
            $model->load([
                $model->formName() => [
                    'detail' => str_repeat('a', 500),
                    'url' => '/path/to/url' . str_repeat('a', 2000 - strlen('/path/to/url')),
                    'valid_chk' => CustomField::INVALID,
                ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * 公開状況の値のラベルテスト
     */
    public function testValidChkLabel()
    {
        verify(CustomField::validChkLabel()[CustomField::VALID])->equals('公開');
        verify(CustomField::validChkLabel()[CustomField::INVALID])->equals('非公開');
    }

    /**
     * バリデーション前処理テスト
     */
    public function testBeforeValidate()
    {
        $this->specify('新規作成時', function () {
            $model = new CustomField();
            $model->beforeValidate();
            verify($model->custom_no)->equals(CustomField::find()->max('custom_no') + 1);
        });
        $this->specify('更新時', function () {
            /** @var CustomField $model */
            $model = (new CustomField())->find()->one();
            $customNo = $model->custom_no;
            $model->beforeValidate();
            verify($model->custom_no)->equals($customNo);
        });
    }

    public function testDeleteOldFile()
    {
        // テスト用レコードとファイルを準備
        $model = new CustomField();
        $this->loadFilePost($model->formName(), 'pict');
        $model->load([
            'detail' => 'データ取得テスト',
            'url' => '/this/is/deleteOldFile/test/',
            'valid_chk' => CustomField::VALID,
        ], '');
        $model->save();
        $model->saveFiles();

        // 検証に使うUploaderクラスを準備
        $uploader = new Uploader();
        $uploader->dirPath = CustomField::DIR_PATH;

        // レコードが存在する状態で実行してもファイルは消されない
        $model = CustomField::findOne(['url' => '/this/is/deleteOldFile/test/']);
        $model->deleteOldFile();
        verify($uploader->hasStorage($model->pict))->true();

        // レコードを消してから実行するとファイルが削除される
        $model->delete();
        $model->deleteOldFile();
        verify($uploader->hasStorage($model->pict))->false();
    }

    /**
     * pictファイルが他で使われているかどうか返すテスト
     */
    public function testIsUsedPict()
    {
        $model = new CustomField();
        // 検証用のデータを用意
        $pict = 'isUsedPict_test_pict.jpg';
        $attributes = [
            'detail' => 'データ取得テスト',
            'url' => '/this/is/isUsedPict/test/',
            'valid_chk' => CustomField::VALID,
        ];
        // 検証用データを保存
        $model->load([$model->formName() => $attributes]);
        $model->pict = $pict;
        $model->deleteFileFlg = true; // oldAttributeが上書きされるのを防止するため削除フラグを立てる
        $model->save();

        verify(CustomField::isUsedPict($pict))->true();

        // データを削除する
        $model->delete();
        verify(CustomField::isUsedPict($pict))->false();
    }

    /**
     * URLが一致するデータがあればHTMLを返すテスト
     */
    public function testCustomFieldHtml()
    {
        $this->specify('公開', function () {
            // todo 通らないので修正する
            // 既存の公開フラグデータを取得する
            /** @var CustomField $model */
            $model = (new CustomField())->find()->where(['valid_chk' => CustomField::VALID])->one();
            // URLが一致するデータを取得
            $resHtml = CustomField::customFieldHtml($model->url);
            $imagePath = Html::img(null);
            $detail = Html::tag('p', Html::encode($model->detail), ['class' => 'resultCustomField__text']);
            $html = <<<HTML
<div class="resultCustomField clearfix">
    <div class="resultCustomField__image">$imagePath</div>
     $detail
</div>
HTML;
            verify($resHtml)->equals($html);
        });
        $this->specify('非公開', function () {
            // 既存の非公開フラグデータを取得する
            /** @var CustomField $model */
            $model = (new CustomField())->find()->where(['valid_chk' => CustomField::INVALID])->one();
            // URLが一致するデータを取得
            $resHtml = CustomField::customFieldHtml($model->url);
            verify($resHtml)->equals('');
        });
    }

    /**
     * 有効なURLをすべて取得するテスト
     */
    public function testAllUrls()
    {
        $this->specify('数量', function () {
            $model = new CustomField();
            $totalCount = $model->find()->where([
                'valid_chk' => CustomField::VALID,
            ])->count();
            verify(CustomField::allUrls())->count((int)$totalCount);
        });
        $this->specify('内容', function () {
            $urls = CustomField::allUrls();
            $model = new CustomField();
            foreach ((array)$urls as $url) {
                $query = $model->find()->where([
                    CustomField::tableName() . '.url' => $url,
                ]);
                verify($query->exists())->true();
            }
        });
    }
}