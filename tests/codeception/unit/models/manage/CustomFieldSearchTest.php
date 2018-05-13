<?php
/**
 * Created by PhpStorm.
 * User: lumitec
 * Date: 2017/05/24
 * Time: 21:17
 */

namespace models\manage;

use app\commands\components\Uploader;
use app\models\manage\CustomField;
use app\models\manage\CustomFieldSearch;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;

/**
 * Class CustomFieldSearchTest
 * @package models\manage
 */
class CustomFieldSearchTest extends JmTestCase
{

    /**
     * rules test
     */
    public function testRules()
    {
        $this->specify('文字列チェック', function () {
            $model = new CustomFieldSearch();
            $model->load([
                $model->formName() => [
                    'url' => (int)1,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('url'))->true();
        });
        $this->specify('正しいチェック', function () {
            $model = new CustomFieldSearch();
            $model->load([
                $model->formName() => [
                    'url' => 'test',
                ],
            ]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索test
     * todo 通らないため修復
     */
    public function testSearch()
    {
        $search = new CustomFieldSearch();
        /** @var CustomField $model */
        $model = (new CustomField())->find()->one();
        $params = [
            'CustomFieldSearch' => ['url' => $model->url],
        ];
        $dataProvider = $search->search($params);
        verify(ArrayHelper::toArray($dataProvider->query->all()))->equals($model->getAttributes());
    }

    /**
     * CSVダウンロードテスト
     * todo 枠しかないため作成
     */
    public function testCsvSearch()
    {

    }

    public function testDeleteFiles()
    {
        // テスト用レコードとファイルを準備
        $model = new CustomField();
        $this->loadFilePost($model->formName(), 'pict');
        $model->load([
            'detail' => 'データ取得テスト',
            'url' => '/this/is/deleteFiles/test/',
            'valid_chk' => CustomField::VALID,
        ], '');
        $model->save();
        $model->saveFiles();

        // 検証に使うUploaderクラスを準備
        $uploader = new Uploader();
        $uploader->dirPath = CustomField::DIR_PATH;

        // レコードが存在する状態で実行してもファイルは消されない
        $model = CustomField::findOne(['url' => '/this/is/deleteFiles/test/']);
        CustomFieldSearch::deleteFiles([$model]);
        verify($uploader->hasStorage($model->pict))->true();

        // レコードを消してから実行するとファイルが削除される
        $model->delete();
        CustomFieldSearch::deleteFiles([$model]);
        verify($uploader->hasStorage($model->pict))->false();
    }
}
