<?php
namespace tests\modules\manage\models\manage;

use app\commands\components\Uploader;
use app\models\FreeContent;
use app\models\FreeContentElement;
use app\modules\manage\models\search\FreeContentSearch;
use tests\codeception\unit\JmTestCase;

/**
 * Class FreeContentSearchTest
 * @package tests\modules\manage\models\manage
 */
class FreeContentSearchTest extends JmTestCase
{
    /**
     * rulesのtest
     */
    public function testRules()
    {
        $this->specify('boolチェック', function () {
            $model = new FreeContentSearch();
            $model->valid_chk = 10;
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('正常値', function () {
            $model = new FreeContentSearch();
            $model->valid_chk = 0;
            $model->validate();
            verify($model->hasErrors('valid_chk'))->false();
        });
    }

    /**
     * searchのtest
     * 同時にsearchWhereの検証もしている
     */
    public function testSearch()
    {
        $this->specify('初期', function () {
//            verify($this->searchQuery([])->count())->equals(0);
        });

        $this->specify('何も無しで検索', function () {
            $query = $this->searchQuery(['FreeContentSearch' => ['valid_chk' => '']]);
            verify($query->count())->equals(FreeContent::find()->count());
        });

        $this->specify('クリアボタン', function () {
            $query = $this->searchQuery(['FreeContentSearch' => 1]);
            verify($query->count())->equals(FreeContent::find()->count());
        });

        $this->specify('有効のみ', function () {
            /** @var FreeContentSearch[] $models */
            $models = $this->searchQuery(['FreeContentSearch' => ['valid_chk' => 1]])->all();
            foreach ($models as $model) {
                verify($model->valid_chk)->equals(FreeContent::VALID);
            }
        });
    }

    /**
     * deleteSearchのtest
     */
    public function testDeleteSearch()
    {
        $targetIds = FreeContent::find()->select('id')->limit(3)->column();
        $id1 = $targetIds[0];
        $id2 = $targetIds[1];
        $id3 = $targetIds[2];

        $searchParams = ['valid_chk' => ''];

        //-------------------------------
        // オールチェックなし・個別チェックあり
        //-------------------------------
        $totalCount = FreeContent::find()->count();
        $model = new FreeContentSearch();
        $params = [
            $model->formName() => $searchParams,
            'gridData' => json_encode([
                'searchParams' => [$model->formName() => $searchParams],
                'totalCount' => $totalCount,
                'allCheck' => false,
                'selected' => [strval($id1), strval($id3)],
            ]),
        ];
        $ids = $model->deleteSearch($params);
        verify($ids)->count(2);
        verify($ids)->contains($id1);
        verify($ids)->contains($id3);

        //-------------------------------
        // オールチェックあり・個別チェックあり
        //-------------------------------
        $totalCount = FreeContent::find()->count();
        $model = new FreeContentSearch();
        $params = [
            $model->formName() => $searchParams,
            'gridData' => json_encode([
                'searchParams' => [$model->formName() => $searchParams],
                'totalCount' => $totalCount,
                'allCheck' => true,
                'selected' => [strval($id2)],
            ]),
        ];
        $ids = $model->deleteSearch($params);
        verify($ids)->count($totalCount - 1);
        verify($ids)->contains($id1);
        verify($ids)->notContains($id2);
        verify($ids)->contains($id3);
    }

    /**
     * deleteAllDataのtest
     */
    public function testDeleteAllData()
    {
        // 削除するレコードを抽出
        /** @var FreeContent[] $models */
        $models = FreeContent::find()->innerJoinWith('elements')->where([
            'and',
            ['not', [FreeContentElement::tableName() . '.image_file_name' => '']],
            ['not', [FreeContentElement::tableName() . '.image_file_name' => null]],
        ])->limit(2)->all();
        $model1 = $models[0];
        $model2 = $models[1];
        $img1 = $model1->elements[0]->image_file_name;
        $img2 = $model2->elements[0]->image_file_name;
        // レコードに合わせて画像をアップロード
        $uploader = new Uploader();
        $uploader->dirPath = 'free-content';
        $uploader->fileSystem->put($uploader->storageDir() . '/' . $img1, '');
        $uploader->fileSystem->put($uploader->storageDir() . '/' . $img2, '');
        verify($uploader->hasStorage($img1))->true();
        verify($uploader->hasStorage($img2))->true();

        // 削除
        FreeContentSearch::deleteAllData([$model1->id, $model2->id]);
        // レコードが消えている
        $this->tester->dontSeeInDatabase(FreeContent::tableName(), ['id' => $model1->id]);
        $this->tester->dontSeeInDatabase(FreeContent::tableName(), ['id' => $model2->id]);
        // relationレコードも消えている
        $this->tester->dontSeeInDatabase(FreeContentElement::tableName(), ['free_content_id' => $model1->id]);
        $this->tester->dontSeeInDatabase(FreeContentElement::tableName(), ['free_content_id' => $model2->id]);
        // 画像も消えている
        verify($uploader->hasStorage($img1))->false();
        verify($uploader->hasStorage($img2))->false();
        // レコードをリセット
        static::getFixtureInstance('free_content')->initTable();
        static::getFixtureInstance('free_content_element')->initTable();
    }

    /**
     * @param $params
     * @return \yii\db\QueryInterface
     */
    private function searchQuery($params)
    {
        $model = new FreeContentSearch();
        return $model->search($params)->query;
    }
}
